/**
 * @module ConcurrencyManager
 */

import { AxiosInstance, AxiosRequestConfig, AxiosResponse } from "axios";

export interface IConcurrencyQueueItem {
  request: AxiosRequestConfig;
  resolver: (queuedRequest: AxiosRequestConfig) => any;
}

/**
 * Handling and limiting concurrent requests for the API.
 * @param {AxiosInstance} axios   Reference to the caller instance
 * @param {number=10} limit       How many requests to allow at once
 *
 * Based on https://github.com/bernawil/axios-concurrency/blob/master/index.js
 */
export const concurrencyManager = (axios: AxiosInstance, limit: number = 10) => {
  if (limit < 1) {
    throw new Error("ConcurrencyManager Error: minimun concurrent requests is 1");
  }

  const instance = {
    queue: [] as IConcurrencyQueueItem[],
    running: [] as IConcurrencyQueueItem[],
    interceptors: {
      request: 0,
      response: 0,
    },
    shiftInitial(): void {
      setTimeout(() => {
        if (instance.running.length < limit) {
          instance.shift();
        }
      }, 0);
    },
    push(reqHandler: IConcurrencyQueueItem) {
      instance.queue.push(reqHandler);
      instance.shiftInitial();
    },
    shift(): void {
      if (instance.queue.length) {
        const queued = instance.queue.shift();
        if (queued) {
          queued.resolver(queued.request);
          instance.running.push(queued);
        }
      }
    },
    // use as interceptor. Queue outgoing requests
    requestHandler(req: AxiosRequestConfig): Promise<AxiosRequestConfig> {
      return new Promise(resolve => {
        instance.push({
          request: req,
          resolver: resolve,
        } as IConcurrencyQueueItem);
      });
    },
    // use as interceptor. Execute queued request upon receiving a response
    responseHandler(res: AxiosResponse): AxiosResponse {
      instance.running.shift();
      instance.shift();
      return res;
    },
    responseErrorHandler(res: AxiosResponse): any {
      return Promise.reject(instance.responseHandler(res));
    },
    detach(): void {
      axios.interceptors.request.eject(instance.interceptors.request);
      axios.interceptors.response.eject(instance.interceptors.response);
    },
  };

  // queue concurrent requests
  instance.interceptors.request = axios.interceptors.request.use(instance.requestHandler);
  instance.interceptors.response = axios.interceptors.response.use(
    instance.responseHandler,
    instance.responseErrorHandler
  );

  return instance;
};
