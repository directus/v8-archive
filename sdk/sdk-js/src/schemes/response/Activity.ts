import { IAPIMetaList, IAPIResponse } from "../APIResponse";
import { IActivity } from "../directus/Activity";

/**
 * @see https://docs.directus.io/api/reference.html#activity
 */
export interface IActivityResponse extends IAPIResponse<IActivity[], IAPIMetaList> {}
