/**
 * @module Configuration
 */

import { AuthModes } from "./Authentication";

const STORAGE_KEY = "directus-sdk-js";

// defining needed methods for the abstract storage adapter
export interface IStorageAPI {
  getItem<T extends any = any>(key: string): T;
  setItem(key: string, value: any): void;
  removeItem(key: string): void;
}

// configuration merged with defaults
export interface IConfigurationValues {
  url: string;
  project: string;
  token?: string;
  localExp?: number;
  tokenExpirationTime?: number;
  persist: boolean;
  mode: AuthModes;
  storage?: IStorageAPI;
}

export interface IConfiguration {
  token?: string;
  url: string;
  project: string;
  localExp?: number;
  tokenExpirationTime?: number;
  persist: boolean;
  mode: AuthModes;
  dehydrate(): IConfigurationValues | undefined;
  deleteHydratedConfig(): void;
  hydrate(config: IConfigurationValues): void;
  partialUpdate(config: Partial<IConfigurationValues>): void;
  reset(): void;
  update(config: IConfigurationValues): void;
}

// default settings
export interface IConfigurationDefaults {
  tokenExpirationTime: number;
  mode: AuthModes;
}

// constructor options
export interface IConfigurationOptions {
  /**
   * The URL of the direcuts CMS
   */
  url: string;
  /**
   * The token to authenticate if preferred
   */
  token?: string;
  /**
   * Project namespace
   */
  project: string;
  /**
   * Default login expiration as number in ms
   */
  localExp?: number;
  /**
   * If the token should be persitated or rehydrated
   */
  persist?: boolean;
  /**
   * Whether to use cookies or JWTs
   */
  mode: AuthModes;
  /**
   * Auto token expiration time
   */
  tokenExpirationTime?: number;


  storage?: IStorageAPI;
}

/**
 * Configuration holder for directus implementations
 * @author Jan Biasi <biasijan@gmail.com>
 */
export class Configuration implements IConfiguration {
  /**
   * Defaults for all directus sdk instances, can be modified if preferred
   * @type {IConfigurationDefaults}
   */
  public static defaults: IConfigurationDefaults = {
    tokenExpirationTime: 5 * 6 * 1000,
    mode: "jwt",
  };

  /**
   * Saves the internal configuration values, **DO NOT modify** from the outside
   * @internal
   */
  private internalConfiguration: IConfigurationValues;

  /**
   * Creates a new configuration instance, will be used once for each directus instance (passing refs).
   * @constructor
   * @param {IConfigurationOptions} initialConfig   Initial configuration values
   * @param {IStorageAPI?} storage                  Storage adapter for persistence
   */
  constructor(initialConfig: IConfigurationOptions = {} as any, private storage?: IStorageAPI) {
    let dehydratedConfig: IConfigurationValues = {} as IConfigurationValues;

    if (storage && Boolean(initialConfig && initialConfig.persist)) {
      // dehydrate if storage was provided and persist flag is set
      dehydratedConfig = this.dehydratedInitialConfiguration(storage);
    }

    const persist = Boolean(dehydratedConfig.persist || initialConfig.persist);
    const project = dehydratedConfig.project || initialConfig.project;
    const mode = dehydratedConfig.mode || initialConfig.mode || Configuration.defaults.mode;
    const tokenExpirationTime =
      dehydratedConfig.tokenExpirationTime ||
      initialConfig.tokenExpirationTime ||
      Configuration.defaults.tokenExpirationTime;

    this.internalConfiguration = {
      ...initialConfig,
      ...dehydratedConfig,
      persist,
      mode,
      project,
      tokenExpirationTime,
    };
  }

  // ACCESSORS =================================================================

  public get token(): string | undefined {
    return this.internalConfiguration.token;
  }

  public set token(token: string | undefined) {
    this.partialUpdate({ token });
  }

  public get tokenExpirationTime(): number | undefined {
    return this.internalConfiguration.tokenExpirationTime;
  }

  public set tokenExpirationTime(tokenExpirationTime: number | undefined) {
    if (typeof tokenExpirationTime === "undefined") return;

    // TODO: Optionally re-compute the localExp property for the auto-refresh
    this.partialUpdate({
      tokenExpirationTime: tokenExpirationTime * 60000,
    });
  }

  public get url(): string {
    return this.internalConfiguration.url;
  }

  public set url(url: string) {
    this.partialUpdate({ url });
  }

  public get project(): string {
    return this.internalConfiguration.project;
  }

  public set project(project: string) {
    this.partialUpdate({
      project: project,
    });
  }

  public get localExp(): number | undefined {
    return this.internalConfiguration.localExp;
  }

  public set localExp(localExp: number | undefined) {
    this.partialUpdate({ localExp });
  }

  public get persist(): boolean {
    return this.internalConfiguration.persist;
  }

  public set persist(persist: boolean) {
    this.internalConfiguration.persist = persist;
  }

  public get mode(): AuthModes {
    return this.internalConfiguration.mode;
  }

  public set mode(mode: AuthModes) {
    this.internalConfiguration.mode = mode;
  }

  // HELPER METHODS ============================================================

  /**
   * Update the configuration values, will also hydrate them if persistance activated
   * @param {IConfigurationValues} config
   */
  public update(config: IConfigurationValues): void {
    this.internalConfiguration = config;

    this.hydrate(config);
  }

  /**
   * Update partials of the configuration, behaves like the [update] method
   * @param {Partial<IConfigurationValues>} config
   */
  public partialUpdate(config: Partial<IConfigurationValues>): void {
    this.internalConfiguration = {
      ...this.internalConfiguration,
      ...config,
    };

    this.hydrate(this.internalConfiguration);
  }

  /**
   * Reset the whole confiugration and remove hydrated values from storage as well
   */
  public reset(): void {
    delete this.internalConfiguration.token;
    delete this.internalConfiguration.url;
    delete this.internalConfiguration.project;
    delete this.internalConfiguration.localExp;

    this.deleteHydratedConfig();
  }

  // STORAGE METHODS ===========================================================

  public dehydrate(): IConfigurationValues | undefined {
    if (!this.storage || !this.persist) {
      return;
    }

    const nativeValue = this.storage.getItem(STORAGE_KEY);

    if (!nativeValue) {
      return;
    }

    const parsedConfig = JSON.parse(nativeValue);
    this.internalConfiguration = parsedConfig;

    return parsedConfig;
  }

  public hydrate(props: IConfigurationValues) {
    // Clears the passed storage to avoid passing itself and going out of memory
    props.storage = undefined
    if (!this.storage || !this.persist) {
      return;
    }

    this.storage.setItem(STORAGE_KEY, JSON.stringify(props));
  }

  public deleteHydratedConfig(): void {
    if (!this.storage || !this.persist) {
      return;
    }

    this.storage.removeItem(STORAGE_KEY);
  }

  private dehydratedInitialConfiguration(storage: IStorageAPI): IConfigurationValues {
    if (!storage) {
      return {} as IConfigurationValues;
    }

    const nativeValue = storage.getItem(STORAGE_KEY);

    if (!nativeValue) {
      return {} as IConfigurationValues;
    }

    try {
      return JSON.parse(nativeValue);
    } catch (err) {
      return {} as IConfigurationValues;
    }
  }
}
