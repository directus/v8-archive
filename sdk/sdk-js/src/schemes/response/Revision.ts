import { IAPIResponse } from "../APIResponse";
import { IRevision } from "../directus/Revision";

/**
 * @see https://docs.directus.io/api/reference.html#revisions
 */
export interface IRevisionResponse<T> extends IAPIResponse<Array<IRevision<T>>> {}
