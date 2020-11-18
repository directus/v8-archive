import { IAPIResponse } from "../APIResponse";

export interface IRefreshTokenResponse extends IAPIResponse<{ token: string }> {}
