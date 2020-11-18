import { IAPIMetaList, IAPIResponse } from "../APIResponse";
import { IField } from "../directus/Field";

interface IFieldResponseMeta {
  Deleted: number;
  Draft: number;
  Published: number;
  table: string;
  total: number;
  total_entries: number;
  type: string;
}

export interface IFieldResponseDataInfo {
  id: string;
  sort: number | null;
  status: number | null;
}

/**
 * @see https://docs.directus.io/api/reference.html#fields-2
 */
export interface IFieldResponse<T extends IField = IField>
  extends IAPIResponse<T & IFieldResponseDataInfo, IFieldResponseMeta> {}

/**
 * @see https://docs.directus.io/api/reference.html#fields-2
 */
export interface IFieldsResponse<T extends IField[] = IField[]>
  extends IAPIResponse<Array<T & IFieldResponseDataInfo>, IAPIMetaList> {}
