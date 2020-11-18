import { ContentCollection } from '../content-collection';
import { NodeRelation } from '../node-relation';

export interface ContentNodeConfig {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  record: any;
  collection: ContentCollection;
  primaryKeyFieldName: string;
}

export class ContentNode {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private _record: any;
  private _collection: ContentCollection;
  private _primaryKeyFieldName: string;
  private _relations: { [fieldId: string]: NodeRelation } = {};

  constructor(config: ContentNodeConfig) {
    this._record = config.record;
    this._primaryKeyFieldName = config.primaryKeyFieldName;
    this._collection = config.collection;
  }

  public get primaryKey(): string {
    return this._record[this._primaryKeyFieldName];
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public get contents(): any {
    return this._record;
  }

  public addRelation(relation: NodeRelation): void {
    this._relations[relation.field] = relation;
  }

  public getRelations(): { [fieldId: string]: NodeRelation } {
    return this._relations;
  }

  public getCollection(): ContentCollection {
    return this._collection;
  }
}
