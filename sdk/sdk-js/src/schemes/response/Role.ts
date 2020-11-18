import { IAPIResponse } from "../APIResponse";
import { IRole } from "../directus/Role";

/**
 * @see https://docs.directus.io/api/reference.html#roles
 */
export interface IRoleResponse extends IAPIResponse<IRole[]> {}
