import { IRelation } from '@directus/sdk-js/dist/types/schemes/directus/Relation';
import { ICollectionDataSet } from '@directus/sdk-js/dist/types/schemes/response/Collection';
import { log } from '../utils';
import { ContentCollection } from './content-collection';
import {
  ContentRelation,
  FileContentRelation,
  JunctionContentRelation,
  SimpleContentRelation,
} from './content-relation';
export interface ContentMeshConfig {
  collections: ICollectionDataSet[];
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  records: { [collectionId: string]: any[] };
  relations: IRelation[];
}

/**
 * A container for the entire content graph
 * as represented in Directus.
 */
export class ContentMesh {
  /** All collections processed, indexed by collection name. */
  private _collections: { [collectionName: string]: ContentCollection } = {};

  constructor(config: ContentMeshConfig) {
    this._collections = this._buildCollections(config);

    this._buildRelations(config.relations).forEach((relation) => relation.applyRecordUpdates());
  }

  /**
   * Builds all relations to be processed by the mesh.
   *
   * @param relations The original relations as returned by Directus.
   */
  private _buildRelations(relations: IRelation[] = []): ContentRelation[] {
    // Could do a single pass over relations, but chose
    // to do two for clarity.
    return [
      ...this._buildO2MRelations(relations),
      ...this._buildM2MRelations(relations),
      ...this._buildFileRelations(),
    ];
  }

  /**
   * Builds all O2M (and M2O) relations to be processed by the mesh.
   *
   * @param relations The original relations as returned by Directus.
   */
  private _buildO2MRelations(relations: IRelation[] = []): ContentRelation[] {
    return relations.reduce((bag, relation) => {
      // eslint-disable-next-line @typescript-eslint/naming-convention
      const { collection_many, collection_one, field_many, field_one } = relation;

      if (!this._shouldProcessRelation(relation, 'o2m')) {
        return bag;
      }

      const destTable = this.getCollection(collection_many);
      const srcTable = this.getCollection(collection_one);

      // Only process relations for tables that exist.
      if (!destTable || !srcTable) {
        // Warn the user if we think the relation should have been processed.
        // eslint-disable-next-line @typescript-eslint/naming-convention
        if (!collection_many.match(/^directus/) || !collection_one.match(/^directus/)) {
          log.warn('Unable to resolve Directus relation', {
            config: relation,
            srcTable,
            destTable,
          });
          log.warn('Have these collections in memory', {
            collections: Object.keys(this._collections),
          });
        }

        return bag;
      }

      // eslint-disable-next-line @typescript-eslint/naming-convention
      log.info(`Creating O2M relation for ${destTable.name}.${field_many} -> ${srcTable.name}.${field_one}`);

      bag.push(
        new SimpleContentRelation({
          // eslint-disable-next-line @typescript-eslint/naming-convention
          destField: field_many,
          destTable,
          // eslint-disable-next-line @typescript-eslint/naming-convention
          srcField: field_one,
          srcTable,
          mesh: this,
        }),
      );

      return bag;
    }, [] as ContentRelation[]);
  }

  /**
   * Builds all M2M relations to be processed by the mesh.
   *
   * @param relations The original relations as returned by Directus
   */
  private _buildM2MRelations(relations: IRelation[] = []): ContentRelation[] {
    return this._resolveRelationPairings(relations).reduce((bag, [a, b]) => {
      const destTable = this.getCollection(a.collection_one);
      const srcTable = this.getCollection(b.collection_one);
      const junctionTable = this.getCollection(a.collection_many);

      if (!destTable || !srcTable || !junctionTable) {
        log.warn('Unable to resolve Directus junction. Missing collections', {
          destTable,
          srcTable,
          junctionTable,
          a,
          b,
        });
        log.warn('Have these tables in memory', {
          collections: Object.keys(this._collections),
        });
        log.warn('This may be a result of Directus keeping deleted junction information in internal tables.');
        return bag;
      }

      log.info(`Creating M2M relation for ${destTable.name} <-> ${srcTable.name}`);

      // Key adjustment to data returned by Directus.
      // The second entry in the IRelation pair has a mismatched `field_one` name if the
      // relation is a self join. This converts it to the correct value as held in the relation record
      // with minimum ID.
      const srcField = destTable.name === srcTable.name ? this._resolveSelfJoinSrcField(a, b) : b.field_one;

      bag.push(
        new JunctionContentRelation({
          destField: a.field_one,
          destJunctionField: a.junction_field,
          destTable,
          srcField,
          srcJunctionField: b.junction_field,
          srcTable,
          junctionTable,
          mesh: this,
        }),
      );

      return bag;
    }, [] as ContentRelation[]);
  }

  private _resolveRelationPairings(relations: IRelation[]): [IRelation, IRelation][] {
    const relationSets: [IRelation, IRelation][] = [];
    const unmatchedRelations: {
      [junctionTableName: string]: IRelation[];
    } = {};

    // Sort relations by ID to ensure we processes them
    // sequentially. Required for M2M self joins to work correctly.
    relations.forEach((relation) => {
      // Only process M2M, non-internal relations
      if (!this._shouldProcessRelation(relation, 'm2m')) {
        return;
      }

      if (!unmatchedRelations[relation.collection_many]) {
        unmatchedRelations[relation.collection_many] = [];
      }

      // A matching relation pair has the same 'collection_many' (junction table),
      // and the 'junction_field' for one matches the 'field_many' for the other.
      // A critical assumption for matching m2mrelation entries is that they are AJDACENT
      // to each other in sorted order, indicated by their IDs.
      const match = unmatchedRelations[relation.collection_many].find(
        (test) =>
          relation.field_many === test.junction_field &&
          relation.junction_field === test.field_many &&
          Math.abs(relation.id - test.id) === 1,
      );

      if (match) {
        relationSets.push([match, relation]);
        unmatchedRelations[relation.collection_many] = unmatchedRelations[relation.collection_many].filter(
          ({ id }) => id !== match.id,
        );
      } else {
        unmatchedRelations[relation.collection_many].push(relation);
      }
    });

    Object.keys(unmatchedRelations).forEach((k) => {
      if (unmatchedRelations[k].length) {
        log.warn(
          'Unable to resolve some M2M relations. No matching relation records were found.',
          unmatchedRelations[k],
        );
      }
    });

    return relationSets;
  }

  private _resolveSelfJoinSrcField(a: IRelation, b: IRelation): string {
    return a.id < b.id ? a.field_one : b.field_one;
  }

  private _buildFileRelations(): ContentRelation[] {
    const fileTable = this.getCollection('directus_files');

    if (!fileTable) {
      log.warn(`Couldn't resolve the internal file table using the name "directus_files"`);
      return [];
    }

    return Object.values(this._collections).reduce((bag, collection) => {
      Object.values(collection.fields)
        .filter(({ type }) => type === 'file')
        .forEach(({ field }) => {
          log.info(`Creating File relation for ${collection.name}.${field}`);

          bag.push(
            new FileContentRelation({
              destField: field,
              destTable: collection,
              mesh: this,
              fileTable,
            }),
          );
        });

      return bag;
    }, [] as ContentRelation[]);
  }

  private _shouldProcessRelation(
    // eslint-disable-next-line @typescript-eslint/naming-convention
    { collection_many, collection_one, junction_field }: IRelation,
    type: 'o2m' | 'm2m',
  ): boolean {
    // eslint-disable-next-line @typescript-eslint/naming-convention
    if (collection_many.match(/^directus/) && collection_one.match(/^directus/)) {
      return false;
    }

    switch (type) {
      case 'o2m':
        // eslint-disable-next-line @typescript-eslint/naming-convention
        return !junction_field;
      case 'm2m':
        // eslint-disable-next-line @typescript-eslint/naming-convention
        return !!junction_field;
      default:
        log.error(`Internal error, unknown relation type: ${type}`);
        return false;
    }
  }

  private _buildCollections({
    collections,
    records,
  }: ContentMeshConfig): { [collectionId: string]: ContentCollection } {
    return collections.reduce((bag, collection) => {
      bag[collection.collection] = new ContentCollection({
        collection,
        records: records[collection.collection],
      });

      return bag;
    }, {} as { [collectionId: string]: ContentCollection });
  }

  public getCollection(id: string): ContentCollection | void {
    return this._collections[id];
  }

  public getCollections(): ContentCollection[] {
    return Object.values(this._collections);
  }
}

export { ContentCollection } from './content-collection';
export { ContentNode } from './content-node';
