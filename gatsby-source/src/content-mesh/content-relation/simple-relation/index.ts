import { ContentRelationConfig, ContentRelation } from '..';
import { ContentNode } from '../../content-node';

export type SimpleContentRelationConfig = ContentRelationConfig;

export class SimpleContentRelation extends ContentRelation {
  constructor(config: SimpleContentRelationConfig) {
    super(config);
  }

  protected _resolveNodeRelation(node: ContentNode, tableType: 'src' | 'dest'): void | ContentNode | ContentNode[] {
    if (tableType === 'src') {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const existing: any[] = node.contents[this._srcField] || [];

      return existing.map((record) => this._destTable.getByRecord(record)).filter((node) => !!node) as ContentNode[];
    } else {
      const existing = node.contents[this._destField];

      if (existing) return this._srcTable.getByRecord(existing);
    }
  }
}
