export interface IServerInformation {
  api: {
    version: string;
    database: string;
    project_name: string;
    project_logo: {
      full_url: string;
      url: string;
    };
  };
  server: {
    general: {
      php_version: string;
      php_api: string;
    };
    max_upload_size: 8388608;
  };
}
