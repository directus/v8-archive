import { AxiosError, AxiosRequestConfig } from "axios";

export interface IErrorResponseData {
  error?: {
    code: string;
    message: string;
  };
}

export interface IErrorResponse extends AxiosError {
  request?: object;
  response?: IErrorResponseMeta;
}

export interface IErrorResponseMeta {
  json?: boolean;
  error: Error;
  data: IErrorResponseData;
  status: number;
  statusText: string;
  headers: Record<string, string>;
  config: AxiosRequestConfig;
}
