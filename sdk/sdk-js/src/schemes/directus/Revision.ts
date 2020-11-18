/**
 * @see https://docs.directus.io/api/reference.html#revision-object
 */
export interface IRevision<T> {
  activity: number;
  collection: string;
  data: T;
  delta: Partial<T>;
  id: number;
  item: string;
  parent_changed: boolean;
  parent_collection?: any;
  parent_item?: string;
}
