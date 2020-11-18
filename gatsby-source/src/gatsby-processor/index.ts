import createNodeHelpers from 'gatsby-node-helpers';
import { ContentMesh } from '../content-mesh';
import { GatsbyType } from './gatsby-type';

export interface GatsbyProcessorConfig {
  typePrefix?: string;
  includeJunctions?: boolean;
  downloadFiles?: boolean;
}

export class GatsbyProcessor {
  private _typePrefix = 'Directus';
  private _includeJunctions = false;
  private _downloadFiles = true;

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public gatsby: any;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public createNodeFactory: any;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public generateNodeId: any;

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  constructor(config: GatsbyProcessorConfig, gatsby: any) {
    if (typeof config.typePrefix === 'string') {
      this._typePrefix = config.typePrefix;
    }

    if (typeof config.includeJunctions === 'boolean') {
      this._includeJunctions = config.includeJunctions;
    }

    if (typeof config.downloadFiles === 'boolean') {
      this._downloadFiles = config.downloadFiles;
    }

    const { createNodeFactory, generateNodeId } = createNodeHelpers({
      typePrefix: this._typePrefix,
    });

    this.createNodeFactory = createNodeFactory;
    this.generateNodeId = generateNodeId;
    this.gatsby = gatsby;
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public async processMesh(mesh: ContentMesh): Promise<any[]> {
    const nodes = await Promise.all(
      mesh
        .getCollections()
        .filter(({ isJunction }) => !isJunction || this._includeJunctions)
        .map((collection) => new GatsbyType(collection, this).buildNodes()),
    );

    return Promise.all(
      nodes
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        .reduce((flattened, nodes) => [...flattened, ...nodes], [] as any[])
        .map((node) => this.gatsby.actions.createNode(node)),
    );
  }

  public get downloadFiles(): boolean {
    return this._downloadFiles;
  }
}
