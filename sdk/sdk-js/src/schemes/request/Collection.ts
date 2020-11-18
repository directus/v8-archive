import { ICollectionPreset } from "../directus/CollectionPreset";

interface ICollectionPresetData extends ICollectionPreset {
  title: string;
  view_query: {
    tabular: {
      fields: string[];
      sort?: string;
    };
  };
  // TODO: Specify fields
  [otherFields: string]: any;
}

export interface ICreateCollectionPresetBody extends ICollectionPresetData {}

export interface IUpdateCollectionPresetBody extends Partial<ICollectionPresetData> {}
