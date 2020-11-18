import { IAPIMetaList, IAPIResponse } from "../APIResponse";

/**
 * @see https://docs.directus.io/api/reference.html#items
 */
export interface IItemResponse<ItemType extends {} = {}> extends IAPIResponse<ItemType> {}

/**
 * @see https://docs.directus.io/api/reference.html#items
 */
export interface IItemsResponse<ItemsType extends Array<{}> = Array<{}>>
  extends IAPIResponse<ItemsType, IAPIMetaList> {}
