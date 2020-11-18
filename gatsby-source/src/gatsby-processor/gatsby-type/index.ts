import Pluralize from 'pluralize';
import { GatsbyProcessor } from '..';
import { ContentCollection, ContentNode } from '../../content-mesh';
import { GatsbyFileNode, GatsbyNode } from '../gatsby-node';

export class GatsbyType {
  private _nodes: GatsbyNode[];
  private _name: string;
  private _collection: ContentCollection;

  private _processor: GatsbyProcessor;

  constructor(collection: ContentCollection, processor: GatsbyProcessor) {
    this._processor = processor;
    this._collection = collection;
    this._name = GatsbyType.getTypeName(collection);
    this._nodes = this._initNodes(collection.getNodes());
  }

  public static getTypeName({ name }: ContentCollection): string {
    const singularName = Pluralize.isPlural(name) ? Pluralize.singular(name) : name;
    const strippedName = name.match(/^directus/) ? singularName.replace('directus', '') : singularName;

    return `${strippedName[0].toUpperCase()}${strippedName.slice(1)}`;
  }

  private _initNodes(nodes: ContentNode[] = []): GatsbyNode[] {
    return nodes.map((node) => {
      if (this._collection.isFileCollection && this._processor.downloadFiles) {
        return new GatsbyFileNode(node, this._processor);
      }

      return new GatsbyNode(node, this._processor);
    });
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public async buildNodes(): Promise<any[]> {
    const formatter = this._processor.createNodeFactory(this._name);

    const nodes = await Promise.all(this._nodes.map((node) => node.build()));

    return nodes.map((node) => formatter(node));
  }

  public get name(): string {
    return this._name;
  }
}
