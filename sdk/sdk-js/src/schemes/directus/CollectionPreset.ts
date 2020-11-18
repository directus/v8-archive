import { ITranslation } from "./Translation";

interface IRequiredCollectionPresetData {
  title: string;
}

interface IOptionalCollectionPresetData {
  role: number;
  collection: string;
  search_query: string;
  filters: object;
  view_type: string;
  view_query: object;
  view_options: object;
  translation: ITranslation;
}

/**
 * @see https://docs.directus.io/api/reference.html#collection-preset-object
 */
export interface ICollectionPreset extends IRequiredCollectionPresetData, Partial<IOptionalCollectionPresetData> {}
