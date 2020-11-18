import { FilterOperator } from "./Filter";

export type MetadataParams =
  | 'type'
  | 'collection'
  | 'table'
  | 'result_count'
  | 'total_count'
  | 'filter_count'
  | 'status'
  | 'page'

/**
 * @internal
 */
interface IQueryParameters {
  meta: MetadataParams[] | MetadataParams | '*';
  fields: string | string[];
  limit: number;
  offset: number;
  single: number;
  sort: string | string[];
  status: string | string[];
  filter: {
    [field: string]: { [operator in FilterOperator]?: any };
  };
  lang: string;
  q: string;
  groups: string | string[];
  activity_skip: number;
  comment: string;
}

/**
 * @see https://docs.directus.io/api/reference.html#query-parameters
 */
export type QueryParams = Partial<IQueryParameters>;


/**
 * @internal
 */
interface IAssetQueryParameters {
  key: string;
  w: number;
  h: number;
  f: string;
  q: number;
}

/**
 * @see https://docs.directus.io/api/reference.html#query-parameters
 */
export type AssetQueryParams = Partial<IAssetQueryParameters>;
