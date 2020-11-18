import { AxiosResponse, AxiosRequestConfig } from "axios";

export const mockAxiosResponse = <T extends object>(
  data: T,
  status: number = 200,
  statusText: string = "",
  headers: object = {},
  config: AxiosRequestConfig = {}
): AxiosResponse<T> => ({
  status,
  statusText,
  headers,
  data,
  config,
});
