/**
 * @see https://docs.directus.io/api/reference.html#relation-object
 */
export interface IRelation {
  id: number;
  collection_many: string;
  field_many: string;
  collection_one: string;
  field_one: string;
  junction_field: string;
}
