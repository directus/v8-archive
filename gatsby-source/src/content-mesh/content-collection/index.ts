import { IField } from '@directus/sdk-js/dist/types/schemes/directus/Field';
import { ICollectionDataSet } from '@directus/sdk-js/dist/types/schemes/response/Collection';
import { ContentNode } from '../content-node';

export interface ContentCollectionConfig {
  collection: ICollectionDataSet;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  records: any[];
}

export class ContentCollection {
  private _collection: ICollectionDataSet;
  private _nodes: { [recordId: string]: ContentNode } = {};
  private _primaryKeyFieldName: string;

  private _isJunction = false;

  constructor(config: ContentCollectionConfig) {
    this._collection = config.collection;
    this._primaryKeyFieldName = this._resolvePrimaryKeyFieldName(config.collection);
    this._nodes = this._buildNodes(config);
  }

  private _buildNodes({ records = [] }: ContentCollectionConfig): { [recordId: string]: ContentNode } {
    return records.reduce((bag, record) => {
      const node = new ContentNode({
        record,
        collection: this,
        primaryKeyFieldName: this._primaryKeyFieldName,
      });

      bag[node.primaryKey] = node;

      return bag;
    }, {});
  }

  private _resolvePrimaryKeyFieldName(collection: ICollectionDataSet): string {
    // eslint-disable-next-line @typescript-eslint/naming-convention
    const [pkField] = Object.values(collection.fields).filter(({ primary_key }) => primary_key);

    if (!pkField) {
      throw new Error(`Unable to resolve PK field for collection ${collection.collection}`);
    }

    return (pkField as IField).field;
  }

  public flagJunction(isJunction = true): void {
    this._isJunction = isJunction;
  }

  public get isJunction(): boolean {
    return this._isJunction;
  }

  public get isInternal(): boolean {
    return !!this._collection.collection.match(/^directus/i);
  }

  public get isFileCollection(): boolean {
    return this.name === 'directus_files';
  }

  public acceptsRelations(): boolean {
    return !this.isInternal || !this.isFileCollection;
  }

  public get name(): string {
    return this._collection.collection;
  }

  public get fields(): IField[] {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    return Object.values((this._collection as any).fields);
  }

  public getNodes(): ContentNode[] {
    return Object.values(this._nodes);
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public getByPrimaryKey(pk: any): ContentNode | void {
    return this._nodes[pk];
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public getByRecord(record: any): ContentNode | void {
    const pk = (record || {})[this._primaryKeyFieldName];
    return this.getByPrimaryKey(pk);
  }
}
