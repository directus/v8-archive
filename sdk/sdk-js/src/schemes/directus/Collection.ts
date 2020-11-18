import { ITranslation } from "./Translation";

interface IRequiredCollectionData {
  collection: string;
}

interface IOptionalCollectionData {
  managed: boolean;
  hidden: boolean;
  single: boolean;
  icon: string;
  note: string;
  translation: ITranslation;
}

/**
 * @see https://docs.directus.io/api/reference.html#collection-object
 */
export interface ICollection extends IRequiredCollectionData, Partial<IOptionalCollectionData> {}
