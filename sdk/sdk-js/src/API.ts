/**
 * @module API
 */

import axios, { AxiosInstance } from "axios";

import { Authentication, IAuthentication } from "./Authentication";
import { concurrencyManager } from "./ConcurrencyManager";
import { IConfiguration } from "./Configuration";

// Scheme types
import { BodyType } from "./schemes/http/Body";
import { RequestMethod } from "./schemes/http/Request";
import { IErrorResponse, IErrorResponseData, IErrorResponseMeta } from "./schemes/response/Error";

// Utilities
import { isString } from "./utils/is";
import { getPayload } from "./utils/payload";
import { querify } from "./utils/qs";

export interface IAPI {
  auth: IAuthentication;
  xhr: AxiosInstance;
  concurrent: ReturnType<typeof concurrencyManager>;
  reset(): void;
  get<T extends any = any>(endpoint: string, params?: object): Promise<T>;
  post<T extends any = any>(endpoint: string, body?: BodyType, params?: object): Promise<T>;
  patch<T extends any = any>(endpoint: string, body?: BodyType, params?: object): Promise<T>;
  put<T extends any = any>(endpoint: string, body?: BodyType, params?: object): Promise<T>;
  delete<T extends any = any>(endpoint: string): Promise<T>;
  getPayload<T extends object = object>(): T | null;
  request<T extends any = any>(
    method: RequestMethod,
    endpoint: string,
    params?: object,
    data?: object,
    noProject?: boolean,
    headers?: { [key: string]: string },
    skipParseToJSON?: boolean
  ): Promise<T>;
}

export class APIError extends Error {
  constructor(
    public message: string,
    private info: {
      code: number | string;
      method: string;
      url: string;
      params?: object;
      error?: IErrorResponse;
      data?: any;
    }
  ) {
    super(message); // 'Error' breaks prototype chain here
    Object.setPrototypeOf(this, new.target.prototype); // restore prototype chain
  }

  public get url() {
    return this.info.url;
  }

  public get method() {
    return this.info.method.toUpperCase();
  }

  public get code() {
    return `${this.info.code || -1}`;
  }

  public get params() {
    return this.info.params || {};
  }

  public toString() {
    return [
      "Directus call failed:",
      `${this.method} ${this.url} ${JSON.stringify(this.params)} -`,
      this.message,
      `(code ${this.code})`,
    ].join(" ");
  }
}

/**
 * API definition for HTTP transactions
 * @uses Authentication
 * @uses axios
 * @author Jan Biasi <biasijan@gmail.com>
 */
export class API implements IAPI {
  public auth: IAuthentication;
  public xhr: AxiosInstance;
  public concurrent: ReturnType<typeof concurrencyManager>;

  constructor(private config: IConfiguration) {
    const axiosOptions = {
      paramsSerializer: querify,
      timeout: 10 * 60 * 1000, // 10 min
      withCredentials: false,
    };

    if (config.mode === "cookie") {
      axiosOptions.withCredentials = true;
    }

    this.xhr = axios.create(axiosOptions);

    this.auth = new Authentication(config, {
      post: this.post.bind(this),
      xhr: this.xhr,
      request: this.request.bind(this),
    });

    this.concurrent = concurrencyManager(this.xhr, 10);
  }

  /**
   * Resets the client instance by logging out and removing the URL and project
   */
  public reset(): void {
    this.auth.logout();
    this.config.deleteHydratedConfig();
  }

  /// REQUEST METHODS ----------------------------------------------------------

  /**
   * GET convenience method. Calls the request method for you
   * @typeparam T   response type
   * @return {Promise<T>}
   */
  public get<T extends any = any>(endpoint: string, params: object = {}): Promise<T> {
    return this.request("get", endpoint, params);
  }

  /**
   * POST convenience method. Calls the request method for you
   * @typeparam T   response type
   * @return {Promise<T>}
   */
  public post<T extends any = any>(endpoint: string, body: BodyType = {}, params: object = {}): Promise<T> {
    return this.request<T>("post", endpoint, params, body);
  }

  /**
   * PATCH convenience method. Calls the request method for you
   * @typeparam T   response type
   * @return {Promise<T>}
   */
  public patch<T extends any = any>(endpoint: string, body: BodyType = {}, params: object = {}): Promise<T> {
    return this.request<T>("patch", endpoint, params, body);
  }

  /**
   * PUT convenience method. Calls the request method for you
   * @typeparam T   response type
   * @return {Promise<T>}
   */
  public put<T extends any = any>(endpoint: string, body: BodyType = {}, params: object = {}): Promise<T> {
    return this.request<T>("put", endpoint, params, body);
  }

  /**
   * DELETE convenience method. Calls the request method for you
   * @typeparam T   response type
   * @return {Promise<T>}
   */
  public delete<T extends any = any>(endpoint: string): Promise<T> {
    return this.request<T>("delete", endpoint);
  }

  /**
   * Gets the payload of the current token, return type can be generic
   * @typeparam T   extends object, payload type
   * @return {T}
   */
  public getPayload<T extends object = object>(): T | null {
    if (!isString(this.config.token)) {
      return null;
    }

    return getPayload<T>(this.config.token);
  }

  /**
   * Perform an API request to the Directus API
   * @param {RequestMethod} method    Selected HTTP method
   * @param {string} endpoint         Endpoint definition as path
   * @param {object={}} params        Query parameters
   * @param {object={}} data          Data passed to directus
   * @param {boolean=false} noProject Do not include the `project` in the url (for system calls)
   * @param {object={}} headers       Optional headers to include
   * @param {boolean=false} skipParseToJSON  Whether to skip `JSON.parse` or not
   * @typeparam T                     Response type definition, defaults to `any`
   * @return {Promise<T>}
   */
  public request<T extends any = any>(
    method: RequestMethod,
    endpoint: string,
    params: object = {},
    data?: object,
    noProject: boolean = false,
    headers: { [key: string]: string } = {},
    skipParseToJSON: boolean = false
  ): Promise<T> {
    if (!this.config.url) {
      throw new Error("SDK has no URL configured to send requests to, please check the docs.");
    }

    if (noProject === false && !this.config.project) {
      throw new Error("SDK has no project configured to send requests to, please check the docs.");
    }

    let baseURL = `${this.config.url}`;

    if (baseURL.endsWith("/") === false) baseURL += "/";

    if (noProject === false) {
      baseURL += `${this.config.project}/`;
    }

    const requestOptions = {
      baseURL,
      data,
      headers,
      method,
      params,
      url: endpoint,
    };

    if (this.config.token && isString(this.config.token) && this.config.token.length > 0) {
      requestOptions.headers = headers;
      requestOptions.headers.Authorization = `Bearer ${this.config.token}`;
    }

    if (this.config.project) {
      requestOptions.headers["X-Directus-Project"] = this.config.project;
    }

    return this.xhr
      .request(requestOptions)
      .then((res: { data: any }) => res.data)
      .then((responseData: any) => {
        if (!responseData || responseData.length === 0) {
          return responseData;
        }

        if (typeof responseData !== "object") {
          try {
            return skipParseToJSON ? responseData : JSON.parse(responseData);
          } catch (error) {
            throw {
              data: responseData,
              error,
              json: true,
            };
          }
        }

        return responseData as T;
      })
      .catch((error: IErrorResponse) => {
        const errorResponse: IErrorResponseMeta = error
          ? error.response || ({} as IErrorResponseMeta)
          : ({} as IErrorResponseMeta);
        const errorResponseData: IErrorResponseData = errorResponse.data || ({} as IErrorResponseData);
        const baseErrorInfo = {
          error,
          url: requestOptions.url,
          method: requestOptions.method,
          params: requestOptions.params,
          code: errorResponseData.error ? errorResponseData.error.code || error.code || -1 : -1,
        };

        if (error && error.response && errorResponseData.error) {
          throw new APIError(errorResponseData.error.message || "Unknown error occured", baseErrorInfo);
        } else if (error.response && error.response.json === true) {
          throw new APIError("API returned invalid JSON", {
            ...baseErrorInfo,
            code: 422,
          });
        } else {
          throw new APIError("Network error", {
            ...baseErrorInfo,
            code: -1,
          });
        }
      });
  }
}
