import { ContentCollection } from '../../content-collection';
import { ContentRelation } from '..';
import { ContentNode } from '../../content-node';
import { ContentMesh } from '../..';

export interface FileContentRelationConfig {
  fileTable: ContentCollection;
  destTable: ContentCollection;
  destField: string;
  mesh: ContentMesh;
}

export class FileContentRelation extends ContentRelation {
  constructor(config: FileContentRelationConfig) {
    super({
      destField: config.destField,
      destTable: config.destTable,
      mesh: config.mesh,
      srcTable: config.fileTable,
      srcField: 'id',
    });
  }

  protected _resolveNodeRelation(node: ContentNode, tableType: 'src' | 'dest'): void | ContentNode | ContentNode[] {
    // We won't crete relations for the file nodes.
    if (tableType === 'src') {
      return;
    }

    const existing = node.contents[this._destField];

    if (existing) return this._srcTable.getByRecord(existing);
  }
}
