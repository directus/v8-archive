import { IAPIMetaList, IAPIResponse } from "../APIResponse";
import { ICollectionPreset } from "../directus/CollectionPreset";

/**
 * @see https://docs.directus.io/api/reference.html#collection-presets
 */
export interface ICollectionPresetResponse<CollectionPreset extends ICollectionPreset>
  extends IAPIResponse<CollectionPreset> {}

/**
 * @see https://docs.directus.io/api/reference.html#collection-presets
 */
export interface ICollectionPresetsResponse<CollectionPresets extends ICollectionPreset[]>
  extends IAPIResponse<CollectionPresets, IAPIMetaList> {}
