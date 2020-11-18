import { IAPIMetaList, IAPIResponse } from "../APIResponse";
import { IRelation } from "../directus/Relation";

/**
 * @see https://docs.directus.io/api/reference.html#relations
 */
export interface IRelationResponse extends IAPIResponse<IRelation> {}

/**
 * @see https://docs.directus.io/api/reference.html#relations
 */
export interface IRelationsResponse extends IAPIResponse<IRelation[], IAPIMetaList> {}
