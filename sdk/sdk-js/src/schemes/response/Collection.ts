import { IAPIResponse } from "../APIResponse";
import { ICollection } from "../directus/Collection";
import { IField } from "../directus/Field";

export interface ICollectionDataSet extends ICollection {
  fields: {
    [field: string]: IField;
  };
}

/**
 * @see https://docs.directus.io/api/reference.html#collections
 */
export interface ICollectionResponse extends IAPIResponse<ICollectionDataSet> {}

/**
 * @see https://docs.directus.io/api/reference.html#collections
 */
export interface ICollectionsResponse extends IAPIResponse<ICollectionDataSet[]> {}
