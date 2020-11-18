export interface IAbstractAPIResponse {
  error?: {
    /**
     * @see https://docs.directus.io/api/reference.html#error-codes
     */
    code: number;
    message: string;
  };
}

export interface IAPIResponse<DataType, MetaDataType extends object = {}> extends IAbstractAPIResponse {
  meta: MetaDataType;
  data: DataType;
}

export interface IAPIMetaList {
  result_count: number;
  total_count: number;
}
