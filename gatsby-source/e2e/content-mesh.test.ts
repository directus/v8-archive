import TestServer from './test-server';
import { ContentMesh } from '../src/content-mesh';
import { ContentCollection } from '../src/content-mesh/content-collection';
import { ICollectionDataSet } from '@directus/sdk-js/dist/types/schemes/response/Collection';
import { ContentNode } from '../src/content-mesh/content-node';

/**
 * A helper function used to get a simple test instance
 * of a ContentMesh for a single collection and it's related data.
 *
 * @param collectionName The name of the collection to build the mesh for.
 */
const getBasicMesh = async (
  collectionName: string,
): Promise<{
  mesh: ContentMesh;
  collection: ICollectionDataSet;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  records: any[];
}> => {
  const testDirectusService = TestServer.newDirectusService();

  const { data: collection } = await testDirectusService.getCollection(collectionName);
  const records = await testDirectusService.getCollectionRecords(collectionName);

  const mesh = new ContentMesh({
    collections: [collection],
    records: { [collectionName]: records },
    relations: [],
  });

  return {
    mesh,
    collection,
    records,
  };
};

describe('E2E', () => {
  it('Should initialize the ContentCollection for a simple collection correctly', async () => {
    const collectionName = 'posts';

    const { collection, mesh } = await getBasicMesh(collectionName);
    const { fields } = collection;

    expect(mesh.getCollections().length).toBe(1);

    const col = mesh.getCollection(collectionName) as ContentCollection;

    expect(col).toBeInstanceOf(ContentCollection);
    expect(col.name).toBe(collectionName);
    expect(col.isFileCollection).toBe(false);
    expect(col.isInternal).toBe(false);
    expect(col.isJunction).toBe(false);

    expect(col.fields.length).toBe(Object.keys(fields).length);
    col.fields.forEach((field) => expect(fields[field.field]).toEqual(field));
  });

  it('Should create the ContentNodes for a simple collection correctly', async () => {
    const collectionName = 'posts';

    const { records, mesh } = await getBasicMesh(collectionName);
    const col = mesh.getCollection(collectionName) as ContentCollection;
    const nodes = col.getNodes();

    // Extract primary keys manually
    const recordIds = records.map(({ id }) => id);

    // Just to ensure we're testing a collection that actually has records in it.
    expect(nodes.length).toBeGreaterThan(0);
    expect(nodes.length).toBe(records.length);

    nodes.forEach((node) => {
      expect(node).toBeInstanceOf(ContentNode);
      expect(node.getCollection()).toEqual(col);
      expect(records).toContainEqual(node.contents);
      expect(recordIds).toContain(node.primaryKey);
      expect(node.getRelations()).toEqual({});
    });
  });
});
