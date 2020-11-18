/**
 * @module SDK
 */

// General scheme types types
import { ILoginCredentials, ILoginOptions } from "./schemes/auth/Login";
import { BodyType } from "./schemes/http/Body";
import { QueryParams as QueryParamsType, AssetQueryParams as AssetQueryParamsType } from "./schemes/http/Query";
// Directus scheme types
import { IField } from "./schemes/directus/Field";
import { IRelation } from "./schemes/directus/Relation";
import { IRole } from "./schemes/directus/Role";
import { ICollection } from "./schemes/directus/Collection";
import { ICollectionPreset } from "./schemes/directus/CollectionPreset";
import { IPermission } from "./schemes/directus/Permission";
import { IUser } from "./schemes/directus/User";
// Request schemes
import { IUpdateCollectionPresetBody } from "./schemes/request/Collection";
// Response schemes
import { IAuthenticateResponse } from "./schemes/auth/Authenticate";
import { IRelationResponse, IRelationsResponse } from "./schemes/response/Relation";
import { IActivityResponse } from "./schemes/response/Activity";
import { ICollectionResponse, ICollectionsResponse } from "./schemes/response/Collection";
import { ICollectionPresetResponse } from "./schemes/response/CollectionPreset";
import { IFieldResponse, IFieldsResponse } from "./schemes/response/Field";
import { IFileResponse, IFilesResponse } from "./schemes/response/File";
import { IItemResponse, IItemsResponse } from "./schemes/response/Item";
import { ILogoutResponse } from "./schemes/response/Login";
import { IRevisionResponse } from "./schemes/response/Revision";
import { IRoleResponse } from "./schemes/response/Role";
import { IRefreshTokenResponse } from "./schemes/response/Token";
import { IUserResponse, IUsersResponse } from "./schemes/response/User";
// Utilities
import { getCollectionItemPath } from "./utils/collection";
import { isString } from "./utils/is";
import {querify} from "./utils/qs";
// Manager classes
import { API, IAPI } from "./API";
import { Configuration, IConfiguration, IConfigurationOptions } from "./Configuration";

import { IServerInformationResponse } from "./schemes/response/ServerInformation";
import { ISettingsResponse } from "./schemes/response/Setting";


// TODO: Move to shared types, SDK is the wrong place for that
type PrimaryKeyType = string | number;

/**
 * Main SDK implementation provides the public API to interact with a
 * remote directus instance.
 * @uses API
 * @uses Configuration
 */
export class SDK {
  // api connection and settings
  public config: IConfiguration;
  public api: IAPI;

  // create a new instance with an API
  constructor(options: IConfigurationOptions) {
    this.config = new Configuration(options, options ? options.storage : undefined);
    this.api = new API(this.config);
  }

  // #region authentication

  /**
   * Login to the API; Gets a new token from the API and stores it in this.api.token.
   */
  public login(credentials: ILoginCredentials, options?: ILoginOptions): Promise<IAuthenticateResponse> {
    return this.api.auth.login(credentials, options);
  }

  /**
   * Logs the user out by "forgetting" the token, and clearing the refresh interval
   */
  public logout(): Promise<ILogoutResponse> {
    return this.api.auth.logout();
  }

  /**
   * Resets the client instance by logging out and removing the URL and project
   */
  public reset(): void {
    this.api.reset();
  }

  /**
   * Refresh the token if it is about to expire (within 30 seconds of expiry date).
   * - Calls onAutoRefreshSuccess with the new token if the refreshing is successful.
   * - Calls onAutoRefreshError if refreshing the token fails for some reason.
   * @returns {[boolean, Error?]}
   */
  public refreshIfNeeded(): Promise<[boolean, Error?]> | void {
    return this.api.auth.refreshIfNeeded();
  }

  /**
   * Use the passed token to request a new one
   */
  public refresh(token: string): Promise<IRefreshTokenResponse> {
    return this.api.auth.refresh(token);
  }

  /**
   * Request to reset the password of the user with the given email address.
   * The API will send an email to the given email address with a link to generate a new
   * temporary password.
   */
  public requestPasswordReset<TResponse extends any = any>(email: string, reset_url?: string): Promise<TResponse> {
    const body: any = {
      email
    };
    reset_url ? body.reset_url = reset_url : null;
    return this.api.post<TResponse>("/auth/password/request", body);
  }

  /**
   * Resets the password
   */
  public resetPassword<TResponse extends any = any>(password: string, token: string): Promise<TResponse> {
    const body:any = {
      password,
      token
    };
    return this.api.post<TResponse>("/auth/password/reset", body);
  }

  // #endregion authentication

  // #endregion collection presets

  // #region activity

  /**
   * Get activity
   */
  public getActivity(params: QueryParamsType = {}): Promise<IActivityResponse> {
    return this.api.get<IActivityResponse>("/activity", params);
  }

  // #endregion activity

  // #region bookmarks

  /**
   * Get the bookmarks of the current user
   * @deprecated Will be removed in the next major version, please use {@link SDK.getCollectionPresets} instead
   * @see https://docs.directus.io/advanced/legacy-upgrades.html#directus-bookmarks
   */
  public getMyBookmarks<TResponse extends any[] = any[]>(params: QueryParamsType = {}): Promise<TResponse> {
    return this.getCollectionPresets<TResponse>(params);
  }

  // #endregion bookmarks

  // #region collections

  /**
   * Get all available collections
   */
  public getCollections(params: QueryParamsType = {}): Promise<ICollectionsResponse[]> {
    return this.api.get<ICollectionsResponse[]>("/collections", params);
  }

  /**
   * Get collection info by name
   */
  public getCollection(collection: string, params: QueryParamsType = {}): Promise<ICollectionResponse> {
    return this.api.get<ICollectionResponse>(`/collections/${collection}`, params);
  }

  /**
   * Create a collection
   */
  public createCollection(data: ICollection): Promise<ICollectionResponse> {
    return this.api.post<ICollectionResponse>("/collections", data);
  }

  /**
   * Updates a certain collection
   */
  public updateCollection(collection: string, data: Partial<ICollection>): Promise<ICollectionResponse> {
    return this.api.patch<ICollectionResponse>(`/collections/${collection}`, data);
  }

  /**
   * Deletes a certain collection
   */
  public deleteCollection(collection: string): Promise<void> {
    return this.api.delete<void>(`/collections/${collection}`);
  }

  // #endregion collections

  // #region collection presets

  /**
   * Get the collection presets of the current user
   * @see https://docs.directus.io/api/reference.html#collection-presets
   */
  public async getCollectionPresets<TResponse extends any[] = any[]>(params: QueryParamsType = {}): Promise<TResponse> {
    const { data: user } = await this.getMe({ fields: "*.*" });
    const id = user.id;
    const role = user.role.id;

    return Promise.all([
      this.api.get("/collection_presets", {
        "filter[title][nnull]": 1,
        "filter[user][eq]": id,
      }),
      this.api.get("/collection_presets", {
        "filter[role][eq]": role,
        "filter[title][nnull]": 1,
        "filter[user][null]": 1,
      }),
      this.api.get("/collection_presets", {
        "filter[role][null]": 1,
        "filter[title][nnull]": 1,
        "filter[user][null]": 1,
      }),
    ]).then((values: Array<{ data: any }>) => {
      const [user, role, globalBookmarks] = values;

      return [...(user.data || []), ...(role.data || []), ...(globalBookmarks.data || [])] as TResponse;
    });
  }

  /**
   * Create a new collection preset (bookmark / listing preferences)
   * @see https://docs.directus.io/api/reference.html#collection-presets
   */
  public createCollectionPreset<CollectionPreset extends ICollectionPreset>(
    data: CollectionPreset
  ): Promise<ICollectionPresetResponse<CollectionPreset>> {
    return this.api.post<ICollectionPresetResponse<CollectionPreset>>("/collection_presets", data);
  }

  /**
   * Update collection preset (bookmark / listing preference)
   * @see https://docs.directus.io/api/reference.html#collection-presets
   */
  // tslint:disable-next-line: max-line-length
  public updateCollectionPreset<
    PartialCollectionPreset extends Partial<ICollectionPreset>,
    TResultCollectionPreset extends ICollectionPreset = ICollectionPreset
  >(
    primaryKey: PrimaryKeyType,
    data: IUpdateCollectionPresetBody
  ): Promise<ICollectionPresetResponse<PartialCollectionPreset & TResultCollectionPreset>> {
    return this.api.patch<ICollectionPresetResponse<PartialCollectionPreset & TResultCollectionPreset>>(
      `/collection_presets/${primaryKey}`,
      data
    );
  }

  /**
   * Delete collection preset by primarykey
   * @see https://docs.directus.io/api/reference.html#collection-presets
   */
  public deleteCollectionPreset(primaryKey: PrimaryKeyType): Promise<void> {
    return this.api.delete<void>(`/collection_presets/${primaryKey}`);
  }

  // #endregion collection presets

  // #region extensions

  /**
   * Get the information of all installed interfaces
   * @see https://docs.directus.io/api/reference.html#get-extensions
   */
  public getInterfaces<TResponse extends any[] = any[]>(): Promise<TResponse> {
    return this.api.request<TResponse>("get", "/interfaces", {}, {}, true);
  }

  /**
   * Get the information of all installed layouts
   * @see https://docs.directus.io/api/reference.html#get-extensions
   */
  public getLayouts<TResponse extends any[] = any[]>(): Promise<TResponse> {
    return this.api.request<TResponse>("get", "/layouts", {}, {}, true);
  }

  /**
   * Get the information of all installed modules
   * @see https://docs.directus.io/api/reference.html#get-extensions
   */
  public getModules<TResponse extends any[] = any[]>(): Promise<TResponse> {
    return this.api.request<TResponse>("get", "/modules", {}, {}, true);
  }

  // #endregion extensions

  // #region fields

  /**
   * Get all fields that are in Directus
   * @see https://docs.directus.io/api/reference.html#fields-2
   */
  public getAllFields<TFieldsType extends IField[]>(
    params: QueryParamsType = {}
  ): Promise<IFieldsResponse<TFieldsType>> {
    return this.api.get<IFieldsResponse<TFieldsType>>("/fields", params);
  }

  /**
   * Get the fields that have been setup for a given collection
   * @see https://docs.directus.io/api/reference.html#fields-2
   */
  public getFields<TFieldsType extends IField[]>(
    collection: string,
    params: QueryParamsType = {}
  ): Promise<IFieldsResponse<TFieldsType>> {
    return this.api.get<IFieldsResponse<TFieldsType>>(`/fields/${collection}`, params);
  }

  /**
   * Get the field information for a single given field
   * @see https://docs.directus.io/api/reference.html#fields-2
   */
  public getField<TFieldType extends IField>(
    collection: string,
    fieldName: string,
    params: QueryParamsType = {}
  ): Promise<IFieldResponse<TFieldType>> {
    return this.api.get<IFieldResponse<TFieldType>>(`/fields/${collection}/${fieldName}`, params);
  }

  /**
   * Create a field in the given collection
   * @see https://docs.directus.io/api/reference.html#fields-2
   */
  public createField<TFieldType extends IField>(
    collection: string,
    fieldInfo: TFieldType
  ): Promise<IFieldResponse<TFieldType>> {
    return this.api.post<IFieldResponse<TFieldType>>(`/fields/${collection}`, fieldInfo);
  }

  /**
   * Update a given field in a given collection
   * @see https://docs.directus.io/api/reference.html#fields-2
   */
  public updateField<TFieldType extends Partial<IField>>(
    collection: string,
    fieldName: string,
    fieldInfo: TFieldType
  ): Promise<IFieldResponse<IField & TFieldType> | undefined> {
    return this.api.patch<IFieldResponse<IField & TFieldType>>(`/fields/${collection}/${fieldName}`, fieldInfo);
  }

  /**
   * Update multiple fields at once
   * @see https://docs.directus.io/api/reference.html#fields-2
   * @example
   *
   * // Set multiple fields to the same value
   * updateFields("projects", ["first_name", "last_name", "email"], {
   *   default_value: ""
   * })
   *
   * // Set multiple fields to different values
   * updateFields("projects", [
   *   {
   *     id: 14,
   *     sort: 1
   *   },
   *   {
   *     id: 17,
   *     sort: 2
   *   },
   *   {
   *     id: 912,
   *     sort: 3
   *   }
   * ])
   */
  public updateFields<TFieldsType extends IField[]>(
    collection: string,
    fields: Array<Partial<IField>>
  ): Promise<IFieldsResponse<TFieldsType & IField[]> | undefined>;
  public updateFields<TFieldsType extends IField[]>(
    collection: string,
    fields: string[],
    fieldInfo: Partial<IField>
  ): Promise<IFieldsResponse<TFieldsType & IField[]> | undefined>;
  public updateFields<TFieldsType extends IField[]>(
    collection: string,
    fieldsInfoOrFieldNames: string[] | Array<Partial<IField>>,
    fieldInfo: Partial<IField> | null = null
  ): Promise<IFieldsResponse<TFieldsType & IField[]> | undefined> {
    if (fieldInfo) {
      return this.api.patch(`/fields/${collection}/${fieldsInfoOrFieldNames.join(",")}`, fieldInfo);
    }

    return this.api.patch(`/fields/${collection}`, fieldsInfoOrFieldNames);
  }

  /**
   * Delete a field from a collection
   * @see @see https://docs.directus.io/api/reference.html#fields-2
   */
  public deleteField(collection: string, fieldName: string): Promise<void> {
    return this.api.delete(`/fields/${collection}/${fieldName}`);
  }

  // #endregion fields

  // #region assets

  /**
   * @see https://docs.directus.io/api/reference.html#assets
   */
  public getAssetUrl(privateHash: string, params?: AssetQueryParamsType): string {
    const querystring = params ? querify(params) : "";
    const url = [
      this.config.url.replace(/\/$/, ""),
      this.config.project,
      "assets",
      privateHash
    ].join("/");

    return querystring.length > 0 ? url + "?" + querystring : url;
  }

  /**
   * @see https://docs.directus.io/api/reference.html#assets
   */
  public async getAsset(privateHash: string, params?: AssetQueryParamsType) {
    const previousResponseType = this.api.xhr.defaults.responseType;

    this.api.xhr.defaults.responseType = "arraybuffer";
    const response = this.api.request(
      "get",
      "/assets/" + privateHash,
      params,
      undefined,
      false,
      undefined,
      true
    );
    this.api.xhr.defaults.responseType = previousResponseType;

    return response;
  }

  // #endregion assets

  // #region files

  /**
   * Get a list of available files from Directus
   * @see https://docs.directus.io/api/reference.html#files
   */
  public async getFiles(params: QueryParamsType = {}): Promise<IFilesResponse> {
    return this.api.get("/files", params);
  }

  /**
   * Get a certain file or certain file list from Directus
   * @see https://docs.directus.io/api/reference.html#files
   */
  public async getFile<TFile extends string | string[]>(
    fileName: TFile,
    params: QueryParamsType = {}
  ): Promise<TFile extends string ? IFileResponse : IFilesResponse> {
    const files = typeof fileName === "string" ? fileName : (fileName as string[]).join(",");
    return this.api.get(`/files/${files}`, params);
  }

  /**
   * Upload multipart files in multipart/form-data
   * @see https://docs.directus.io/api/reference.html#files
   */
  public async uploadFiles(
    data: object, // TODO: fix type definition
    onUploadProgress: () => object = () => ({})
  ) {
    let baseURL = `${this.config.url}`;
    if (baseURL.endsWith("/") === false) baseURL += "/";
    baseURL += this.config.project;

    const headers: { [index: string]: any } = {
      "Content-Type": "multipart/form-data",
      "X-Directus-Project": this.config.project,
    };

    if (this.config.token && isString(this.config.token) && this.config.token.length > 0) {
      headers["Authorization"] = `Bearer ${this.config.token}`;
    }

    const response = await this.api.xhr({
      method: 'post',
      url: "/files",
      baseURL,
      data,
      headers,
      onUploadProgress,
      withCredentials: true
    });

    return response.data;
  }

  // #endregion files

  // #region items

  /**
   * Update an existing item
   * @see https://docs.directus.io/api/reference.html#update-item
   * @typeparam TTPartialItem Defining the item type in object schema
   * @typeparam TTResult Extension of [TPartialItem] as expected result
   */
  public updateItem<TTPartialItem extends object, TTResult extends object = TTPartialItem>(
    collection: string,
    primaryKey: PrimaryKeyType,
    body: TTPartialItem,
    params: QueryParamsType = {}
  ): Promise<IItemResponse<TTPartialItem & TTResult>> {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.patch<IItemResponse<TTPartialItem & TTResult>>(`${collectionBasePath}/${primaryKey}`, body, params);
  }

  /**
   * Update multiple items
   * @see https://docs.directus.io/api/reference.html#update-items
   * @typeparam TPartialItem Defining an array of items, each in object schema
   * @typeparam TResult Extension of [TPartialItem] as expected result
   * @return {Promise<IItemsResponse<TPartialItem & TResult>>}
   */
  public updateItems<TPartialItem extends object[], TResult extends TPartialItem = TPartialItem>(
    collection: string,
    body: TPartialItem,
    params: QueryParamsType = {}
  ) {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.patch<IItemsResponse<TPartialItem & TResult>>(collectionBasePath, body, params);
  }

  /**
   * Create a new item
   * @typeparam TItemType Defining an item and its fields in object schema
   * @return {Promise<IItemsResponse<TItemType>>}
   */
  public createItem<TItemType extends object>(collection: string, body: TItemType): Promise<IItemResponse<TItemType>> {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.post<IItemResponse<TItemType>>(collectionBasePath, body);
  }

  /**
   * Create multiple items
   * @see https://docs.directus.io/api/reference.html#create-items
   * @typeparam TItemsType Defining an array of items, each in object schema
   */
  public createItems<TItemsType extends Array<{}>>(
    collection: string,
    body: BodyType
  ): Promise<IItemsResponse<TItemsType>> {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.post<IItemsResponse<TItemsType>>(collectionBasePath, body);
  }

  /**
   * Get items from a given collection
   * @see https://docs.directus.io/api/reference.html#get-multiple-items
   * @typeparam TItemsType Defining an array of items, each in object schema
   */
  public getItems<TTItemsType extends Array<{}>>(
    collection: string,
    params: QueryParamsType = {}
  ): Promise<IItemsResponse<TTItemsType>> {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.get<IItemsResponse<TTItemsType>>(collectionBasePath, params);
  }

  /**
   * Get a single item by primary key
   * @see https://docs.directus.io/api/reference.html#get-item
   * @typeparam TItemType Defining fields of an item in object schema
   */
  public getItem<TItemType extends object = {}>(
    collection: string,
    primaryKey: PrimaryKeyType,
    params: QueryParamsType = {}
  ): Promise<IItemResponse<TItemType>> {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.get<IItemResponse<TItemType>>(`${collectionBasePath}/${primaryKey}`, params);
  }

  /**
   * Delete a single item by primary key
   * @see https://docs.directus.io/api/reference.html#delete-items
   */
  public deleteItem(collection: string, primaryKey: PrimaryKeyType) {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.delete<void>(`${collectionBasePath}/${primaryKey}`);
  }

  /**
   * Delete multiple items by primary key
   * @see https://docs.directus.io/api/reference.html#delete-items
   */
  public deleteItems(collection: string, primaryKeys: PrimaryKeyType[]) {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.delete<void>(`${collectionBasePath}/${primaryKeys.join()}`);
  }

  // #endregion items

  // #region listing preferences

  /**
   * Get the collection presets of the current user for a single collection
   */
  public async getMyListingPreferences<TResponse extends any[] = any[]>(
    collection: string,
    params: QueryParamsType = {}
  ): Promise<TResponse> {
    const { data: user } = await this.getMe({ fields: "*.*" });
    const id = user.id;
    const role = user.role.id;

    return Promise.all([
      this.api.get<IFieldResponse<any>>("/collection_presets", {
        "filter[collection][eq]": collection,
        "filter[role][null]": 1,
        "filter[title][null]": 1,
        "filter[user][null]": 1,
        limit: 1,
        sort: "-id",
      }),
      this.api.get<IFieldResponse<any>>("/collection_presets", {
        "filter[collection][eq]": collection,
        "filter[role][eq]": role,
        "filter[title][null]": 1,
        "filter[user][null]": 1,
        limit: 1,
        sort: "-id",
      }),
      this.api.get<IFieldResponse<any>>("/collection_presets", {
        "filter[collection][eq]": collection,
        "filter[title][null]": 1,
        "filter[user][eq]": id,
        limit: 1,
        sort: "-id",
      }),
    ]).then((values: Array<IFieldResponse<any>>) => {
      const [col, role, user] = values;

      if (user.data && user.data.length > 0) {
        return user.data[0] as TResponse;
      }

      if (role.data && role.data.length > 0) {
        return role.data[0] as TResponse;
      }

      if (col.data && col.data.length > 0) {
        return col.data[0] as TResponse;
      }

      return {} as TResponse;
    });
  }

  // #endregion listing preferences

  // #region permissions

  /**
   * Get permissions
   * @param {QueryParamsType?} params
   * @return {Promise<IPermission>}
   */
  public getPermissions(params: QueryParamsType = {}): Promise<IItemsResponse<IPermission[]>> {
    return this.getItems<IPermission[]>("directus_permissions", params);
  }

  /**
   * TODO: Fix type-def for return
   * Get the currently logged in user's permissions
   * @typeparam TResponse Permissions type as array extending any[]
   */
  public getMyPermissions<TResponse extends any[] = any[]>(params: QueryParamsType = {}): Promise<TResponse> {
    return this.api.get("/permissions/me", params);
  }

  /**
   * TODO: Fix type-def for param and return
   * Create multiple new permissions
   * @typeparam TResponse Permissions type as array extending any[]
   */
  public createPermissions<TResponse extends any[] = any[]>(data: any[]): Promise<TResponse> {
    return this.api.post("/permissions", data);
  }

  /**
   * TODO: Fix type-def for param and return
   * Update multiple permission records
   * @typeparam TResponse Permissions type as array extending any[]
   */
  public updatePermissions<TResponse extends any[] = any[]>(data: any[]): Promise<TResponse> {
    return this.api.patch<TResponse>("/permissions", data);
  }

  // #endregion permissions

  // #region relations

  /**
   * Get all relationships
   * @param {QueryParamsType?} params
   * @return {Promise<IRelationsResponse>}
   */
  public getRelations(params: QueryParamsType = {}) {
    return this.api.get<IRelationsResponse>("/relations", params);
  }

  /**
   * Creates new relation
   * @param {IRelation} data
   * @return {Promise<IRelationResponse>}
   */
  public createRelation(data: IRelation) {
    return this.api.post<IRelationResponse>("/relations", data);
  }

  /**
   * Updates existing relation
   */
  public updateRelation(primaryKey: PrimaryKeyType, data: Partial<IRelation>) {
    return this.api.patch<IRelationResponse>(`/relations/${primaryKey}`, data);
  }

  /**
   * Get the relationship information for the given collection
   */
  public getCollectionRelations(collection: string, params: QueryParamsType = {}): Promise<IRelationsResponse[]> {
    return Promise.all([
      this.api.get<IRelationsResponse>("/relations", {
        "filter[collection_many][eq]": collection,
      }),
      this.api.get<IRelationsResponse>("/relations", {
        "filter[collection_one][eq]": collection,
      }),
    ]);
  }

  // #endregion relations

  // #region revisions

  /**
   * Get a single item's revisions by primary key
   * @typeparam DataAndDelta  The data including delta type for the revision
   * @param {string} collection
   * @param {PrimaryKeyType} primaryKey
   * @param {QueryParamsType?} params
   */
  public getItemRevisions<TDataAndDelta extends object = {}>(
    collection: string,
    primaryKey: PrimaryKeyType,
    params: QueryParamsType = {}
  ): Promise<IRevisionResponse<TDataAndDelta>> {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.get<IRevisionResponse<TDataAndDelta>>(`${collectionBasePath}/${primaryKey}/revisions`, params);
  }

  /**
   * Revert an item to a previous state
   * @param {string} collection
   * @param {PrimaryKeyType} primaryKey
   * @param {number} revisionID
   */
  public revert(collection: string, primaryKey: PrimaryKeyType, revisionID: number): Promise<void> {
    const collectionBasePath = getCollectionItemPath(collection);
    return this.api.patch(`${collectionBasePath}/${primaryKey}/revert/${revisionID}`);
  }

  // #endregion revisions

  // #region roles

  /**
   * Get a single user role
   * @param {PrimaryKeyType} primaryKey
   * @param {QueryParamsType?} params
   */
  public getRole(primaryKey: PrimaryKeyType, params: QueryParamsType = {}): Promise<IRoleResponse> {
    return this.api.get<IRoleResponse>(`/roles/${primaryKey}`, params);
  }

  /**
   * Get the user roles
   * @param {QueryParamsType?} params
   */
  public getRoles(params: QueryParamsType = {}): Promise<IRoleResponse[]> {
    return this.api.get<IRoleResponse[]>("/roles", params);
  }

  /**
   * Update a user role
   * @param {PrimaryKeyType} primaryKey
   * @param {Role} body
   */
  public updateRole<Role extends Partial<IRole>>(primaryKey: PrimaryKeyType, body: Role) {
    return this.updateItem<Role, IRole>("directus_roles", primaryKey, body);
  }

  /**
   * Create a new user role
   * @param {Role} body
   */
  public createRole<TRole extends IRole>(body: TRole) {
    return this.createItem("directus_roles", body);
  }

  /**
   * Delete a user rol by primary key
   * @param {PrimaryKeyType} primaryKey
   */
  public deleteRole(primaryKey: PrimaryKeyType): Promise<void> {
    return this.deleteItem("directus_roles", primaryKey);
  }

  // #endregion roles

  // #region settings

  /**
   * Get Directus' global settings
   * @param {QueryParamsType?} params
   * Limit is hardcoded to -1 because we always want to return all settings
   */
  public getSettings(params: QueryParamsType = {}) {
    params.limit = -1;
    return this.api.get<ISettingsResponse>("/settings", params);
  }

  /**
   * Get the "fields" for directus_settings
   * @param {QueryParamsType?} params
   */
  public getSettingsFields(params: QueryParamsType = {}) {
    return this.api.get<IFieldsResponse>("/settings/fields", params);
  }

  // #endregion settings

  // #region users

  /**
   * Get a list of available users in Directus
   * @param {QueryParamsType?} params
   */
  public getUsers(params: QueryParamsType = {}) {
    return this.api.get<IUsersResponse>("/users", params);
  }

  /**
   * Get a single Directus user
   * @param {PrimaryKeyType} primaryKey
   * @param {QueryParamsType?} params
   */
  public getUser<User extends IUser = IUser>(primaryKey: PrimaryKeyType, params: QueryParamsType = {}) {
    return this.api.get<IUserResponse<User>>(`/users/${primaryKey}`, params);
  }

  /**
   * Get the user info of the currently logged in user
   * @param {QueryParamsType?} params
   */
  public getMe<User extends IUser = IUser>(params: QueryParamsType = {}) {
    return this.api.get<IUserResponse<User>>("/users/me", params);
  }

  /**
   * Create a single Directus user
   * @param {User} body
   */
  public createUser<User extends IUser>(body: User) {
    return this.api.post<IUserResponse<User>>("/users", body);
  }

  /**
   * Update a single user based on primaryKey
   * @param {PrimaryKeyType} primaryKey
   * @param {QueryParamsType?} params
   */
  public updateUser<User extends Partial<IUser & {password?: string}>>(primaryKey: PrimaryKeyType, body: User) {
    return this.updateItem<User, IUser>("directus_users", primaryKey, body);
  }

  // #endregion users

  // #region server admin

  /**
   * This will update the database of the API instance to the latest version
   * using the migrations in the API
   * @return {Promise<void>}
   */
  public updateDatabase(): Promise<void> {
    return this.api.post("/update");
  }

  /**
   * Ping the API to check if it exists / is up and running, returns "pong"
   * @return {Promise<string>}
   */
  public ping(): Promise<string> {
    return this.api.request("get", "/server/ping", {}, {}, true, {}, true);
  }

  /**
   * Get the server info from the API
   * @return {Promise<IServerInformationResponse>}
   */
  public serverInfo(): Promise<IServerInformationResponse> {
    return this.api.request("get", "/", {}, {}, true);
  }

  /**
   * TODO: Add response type-def
   * Get the server info from the project
   * @return {Promise<any>}
   */
  public projectInfo(): Promise<any> {
    return this.api.request("get", "/");
  }

  /**
   * TODO: Add response type-def
   * Get all the setup third party auth providers
   * @return {Promise<any>}
   */
  public getThirdPartyAuthProviders(): Promise<any> {
    return this.api.get("/auth/sso");
  }

  /**
   * Do a test call to check if you're logged in
   * @return {Promise<boolean>}
   */
  public isLoggedIn(): Promise<boolean> {
    return new Promise(resolve => {
      this.api
        .get("/")
        .then(res => {
          if (res.public === undefined) {
            return resolve(true);
          } else {
            return resolve(false);
          }
        })
        .catch(() => resolve(false));
    });
  }

  // #endregion server admin
}
