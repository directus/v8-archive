import { IAPIMetaList, IAPIResponse } from "../APIResponse";
import { ISetting } from "../directus/Setting";

/**
 * @see https://docs.directus.io/api/reference.html#settings
 */
export interface ISettingResponse extends IAPIResponse<ISetting> {}

/**
 * @see https://docs.directus.io/api/reference.html#settings
 */
export interface ISettingsResponse extends IAPIResponse<ISetting[], IAPIMetaList> {}
