import { IStorageAPI, IConfigurationValues } from "../../src/Configuration";

export const mockStorage = <T extends object>(fakeStorage: T): IStorageAPI & { values: IConfigurationValues & T } => {
  const api = {
    values: fakeStorage,
    getItem() {
      return JSON.stringify(api.values);
    },
    setItem(key: string, values: any) {
      api.values = JSON.parse(values);
    },
  };

  return api as any;
};
