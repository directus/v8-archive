import { ContentNode } from '../content-node';

export interface NodeRelationConfig {
  field: string;
  related: ContentNode | ContentNode[];
}

export class NodeRelation {
  public readonly field: string;
  private _related: ContentNode | ContentNode[];

  constructor(config: NodeRelationConfig) {
    this.field = config.field;
    this._related = config.related;
  }

  public getRelatedNodes(): ContentNode | ContentNode[] {
    return this._related;
  }
}
