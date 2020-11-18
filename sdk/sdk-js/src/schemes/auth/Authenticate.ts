import { IUser } from "../directus/User";

export interface IAuthenticateResponse {
  data: {
    token?: string;
    user?: IUser;
  };
}
