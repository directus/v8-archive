import { IAPIResponse } from "../../schemes/APIResponse";
import { IServerInformation } from "../directus/ServerInformation";

/**
 * @see https://docs.directus.io/api/reference.html#information
 */
export interface IServerInformationResponse extends IAPIResponse<IServerInformation> {}
