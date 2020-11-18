import { IAPIMetaList, IAPIResponse } from "../APIResponse";
import { IUser } from "../directus/User";
import { IUserRole } from "../directus/UserRole";

/**
 * @see https://docs.directus.io/api/reference.html#users
 */
export interface IUserResponse<User extends IUser = IUser> extends IAPIResponse<User> {
  id: number;
  roles?: [IUserRole];
}

/**
 * @see https://docs.directus.io/api/reference.html#users
 */
export interface IUsersResponse<Users extends IUser[] = IUser[]> extends IAPIResponse<Users, IAPIMetaList> {}
