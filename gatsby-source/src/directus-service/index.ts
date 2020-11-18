import DirectusSDK from '@directus/sdk-js';
import { IConfigurationOptions } from '@directus/sdk-js/dist/types/Configuration';
import { IFile } from '@directus/sdk-js/dist/types/schemes/directus/File';
import {
  ICollectionDataSet,
  ICollectionResponse,
  // ICollectionsResponse,
} from '@directus/sdk-js/dist/types/schemes/response/Collection';
import { log } from '../utils';
import { ILoginCredentials } from '@directus/sdk-js/dist/types/schemes/auth/Login';

export interface DirectusServiceConfig {
  url: string;

  auth?: {
    email?: string;
    password?: string;
    token?: string;
  };

  project: string;
  fileCollectionName?: string;
  targetStatuses?: string[] | void;

  allowCollections?: string[] | void;
  blockCollections?: string[] | void;

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  customRecordFilter?: (record: any, collection: string) => boolean;
}

export class DirectusService {
  private static _voidStatusKey = '__NONE__';

  private _fileCollectionName = 'directus_files';
  private _targetStatuses: string[] | void = ['published', DirectusService._voidStatusKey];
  private _includeInternalCollections = false;
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private _customRecordFilter?: (record: any, collection: string) => boolean;

  private _allowCollections: string[] | void;
  private _blockCollections: string[] | void;

  private _url: string;
  private _project: string;

  private _api: DirectusSDK;
  private _ready: Promise<void>;

  constructor(config: DirectusServiceConfig) {
    if (!config.project) {
      log.error(`Required config option missing. No 'project' provided.`, { config });
      throw new Error('MALFORMED_CONFIGURATION');
    } else if (typeof config.project !== 'string') {
      log.error(`The provided 'project' config option was invalid. Only strings accepted.`, { config });
      throw new Error('MALFORMED_CONFIGURATION');
    }

    log.info('Initializing Directus Service...');

    if (config.fileCollectionName) {
      this._fileCollectionName = config.fileCollectionName;
    }

    if (Object.prototype.hasOwnProperty.call(config, 'targetStatuses')) {
      this._targetStatuses = config.targetStatuses;
    }

    if (typeof config.customRecordFilter === 'function') {
      this._customRecordFilter = config.customRecordFilter;
    }

    this._allowCollections = config.allowCollections;
    this._blockCollections = config.blockCollections;

    this._url = config.url;
    this._project = config.project;

    this._api = this._initSDK(config);
    this._ready = this._initAuth(config);
  }

  private _initSDK({ url, project, auth = {} }: DirectusServiceConfig): DirectusSDK {
    const config: IConfigurationOptions = {
      url,
      project,
      mode: 'jwt',
    };

    if (auth.token) {
      config.token = auth.token;
      config.persist = true;
    }

    return new DirectusSDK(config);
  }

  private async _initAuth({ auth: { token, email, password } = {} }: DirectusServiceConfig): Promise<void> {
    if (token) {
      return;
    } else if (email && password) {
      return this._login({ email, password, url: this._url, project: this._project });
    }

    log.warn('No authentication details provided. Will try using the public API...');
  }

  private async _login(credentials: ILoginCredentials): Promise<void> {
    try {
      if (!this._api.config.token) {
        const response = await this._api.login(credentials, {
          mode: 'jwt',
          persist: true,
          storage: true,
        });

        if (!response || !response.data.token) {
          throw new Error('Invalid response returned.');
        }

        log.success('Authentication successful.');
      }
    } catch (e) {
      log.warn('Failed to login into Directus using the credentials provided. Will try using the public API...');
    }
  }

  private _shouldIncludeCollection(collection: string, managed = false): boolean {
    if (this._allowCollections && !this._allowCollections.includes(collection)) {
      return false;
    } else if (this._blockCollections && this._blockCollections.includes(collection)) {
      return false;
    }

    return this._includeInternalCollections || managed;
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private _shouldIncludeRecord(record: any, collection: string): boolean {
    const { status } = record;

    if (typeof this._customRecordFilter === 'function') {
      return this._customRecordFilter(record, collection);
    }

    if (!this._targetStatuses) return true;

    if (!status) return this._targetStatuses.includes(DirectusService._voidStatusKey);

    return this._targetStatuses.includes(record.status);
  }

  public async getCollection(collectionId: string): Promise<ICollectionResponse> {
    try {
      await this._ready;
      log.info(`Fetching collection info for "${collectionId}"`);
      const response = await this._api.getCollection(collectionId);

      return response;
    } catch (e) {
      log.error(`Failed to fetch collection ${collectionId}`);
      throw e;
    }
  }

  public async getFilesCollection(): Promise<ICollectionDataSet> {
    try {
      await this._ready;
      log.info(`Fetching files collection using name "${this._fileCollectionName}"`);

      // For some reason, api.getCollection(this._fileCollectionName) is not working
      // at time of authorship.

      // Explicit 'any' cast because ICollectionsResponse doesn't match the actual response shape (DirectusSDK bug)
      const { data: collections = [] } = (await this._api.getCollections({
        limit: -1,
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
      })) as any;

      const [fileCollection] = (collections as ICollectionDataSet[]).filter(
        ({ collection }) => collection === this._fileCollectionName,
      );

      if (!fileCollection) {
        throw new Error('No collection matching the given name found');
      }

      return fileCollection;
    } catch (e) {
      log.error('Failed to fetch files collection');
      throw e;
    }
  }

  public async batchGetCollections(): Promise<ICollectionDataSet[]> {
    try {
      await this._ready;
      log.info('Fetching all collections...');

      // Explicit 'any' cast because ICollectionsResponse doesn't match the actual response shape (DirectusSDK bug)
      const { data: collections = [] } = (await this._api.getCollections({
        limit: -1,
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
      })) as any;

      // Currently we don't consider non-managed Directus tables.
      return (collections as ICollectionDataSet[]).filter(({ collection, managed }) =>
        this._shouldIncludeCollection(collection, managed),
      );
    } catch (e) {
      log.error('Failed to fetch collections');
      throw e;
    }
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public async batchGetRelations(): Promise<any> {
    try {
      await this._ready;
      log.info('Fetching all relations...');

      const { data: relations = [] } = await this._api.getRelations({
        limit: -1,
      });

      return relations;
    } catch (e) {
      log.error('Failed to fetch relations');
      throw e;
    }
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public async getCollectionRecords(collection: string): Promise<any[]> {
    try {
      await this._ready;
      log.info(`Fetching records for ${collection}...`);

      const { data: items = [] } = (await this._api.getItems(collection, {
        fields: '*.*',
        limit: -1,
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
      })) as { data: any[] };

      return items.filter((record) => this._shouldIncludeRecord(record, collection));
    } catch (e) {
      log.error(`Failed to fetch records for collection "${collection}"`);
      log.error(`Did you grant READ permissions?`);
      throw e;
    }
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public batchGetCollectionRecords(collections: ICollectionDataSet[]): Promise<{ [collection: string]: any[] }> {
    log.info('Fetching all records...');

    return Promise.all(collections.map(({ collection }) => this.getCollectionRecords(collection))).then(
      (recordSets = []) => {
        return recordSets.reduce((recordMap, records, i) => {
          recordMap[collections[i].collection] = records || [];
          return recordMap;
          // eslint-disable-next-line @typescript-eslint/no-explicit-any
        }, {} as { [collection: string]: any[] });
      },
    );
  }

  public async getAllFiles(): Promise<IFile[]> {
    try {
      await this._ready;
      log.info('Fetching all files...');

      const { data = [] } = await this._api.getFiles({
        limit: -1,
      });

      // The SDK has 'data' typed as IFile[][], but in reality
      // it's returned as IFile[]
      //
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      return data as any;
    } catch (e) {
      log.error('Failed to fetch files.');
      log.error(`Did you grant READ permissions?`);
      throw e;
    }
  }
}
