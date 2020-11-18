# Environment Variables

## All variables

### General

| Variable                | Type   | Default Value |
| ----------------------- | ------ | ------------- |
| DIRECTUS_ENV            | string | "production"  |
| DIRECTUS_TABLEBLACKLIST | array  | []            |
| DIRECTUS_EXT            | array  | []            |

### Logger

| Variable             | Type   | Default Value |
| -------------------- | ------ | ------------- |
| DIRECTUS_LOGGER_PATH | string | dynamic       |

### Database

| Variable                        | Type    | Default Value |
| ------------------------------- | ------- | ------------- |
| DIRECTUS_DATABASE_TYPE          | string  | "mysql"       |
| DIRECTUS_DATABASE_HOST          | string  | "localhost"   |
| DIRECTUS_DATABASE_PORT          | integer | 3306          |
| DIRECTUS_DATABASE_NAME          | string  | "directus"    |
| DIRECTUS_DATABASE_USERNAME      | string  | "root"        |
| DIRECTUS_DATABASE_PASSWORD      | string  | "root"        |
| DIRECTUS_DATABASE_ENGINE        | string  | "InnoDB"      |
| DIRECTUS_DATABASE_CHARSET       | string  | "utf8mb4"     |
| DIRECTUS_DATABASE_SOCKET        | string  | ""            |
| DIRECTUS_DATABASE_DRIVEROPTIONS | array   | []            |

### Cache

| Variable                   | Type    | Default Value |
| -------------------------- | ------- | ------------- |
| DIRECTUS_CACHE_ENABLED     | boolean | false         |
| DIRECTUS_CACHE_RESPONSETTL | integer | 3600          |

### Cache Pool

| Variable                    | Type    | Default Value |
| --------------------------- | ------- | ------------- |
| DIRECTUS_CACHE_POOL_ADAPTER | string  | "filesystem"  |
| DIRECTUS_CACHE_POOL_PATH    | string  | "../cache/"   |
| DIRECTUS_CACHE_POOL_HOST    | string  | "localhost"   |
| DIRECTUS_CACHE_POOL_PORT    | integer | 6379          |

### Storage

| Variable                        | Type    | Default Value                |
| ------------------------------- | ------- | ---------------------------- |
| DIRECTUS_STORAGE_ADAPTER        | string  | "local"                      |
| DIRECTUS_STORAGE_ROOT           | string  | "public/uploads/_/originals" |
| DIRECTUS_STORAGE_ROOTURL        | string  | "/uploads/_/originals"       |
| DIRECTUS_STORAGE_THUMBROOT      | string  | "public/uploads/_/generated" |
| DIRECTUS_STORAGE_PROXYDOWNLOADS | boolean | false                        |
| DIRECTUS_STORAGE_KEY            | string  | "s3-key"                     |
| DIRECTUS_STORAGE_SECRET         | string  | "s3-secret"                  |
| DIRECTUS_STORAGE_REGION         | string  | "s3-region"                  |
| DIRECTUS_STORAGE_VERSION        | string  | "s3-version"                 |
| DIRECTUS_STORAGE_BUCKET         | string  | "s3-bucket"                  |
| DIRECTUS_STORAGE_ENDPOINT       | string  | "s3-endpoint"                |
| DIRECTUS_STORAGE_OSSACCESSID    | string  | "oss-access-id"              |
| DIRECTUS_STORAGE_OSSACCESSKEY   | string  | "oss-access-secret"          |
| DIRECTUS_STORAGE_OSSBUCKET      | string  | "oss-bucket"                 |
| DIRECTUS_STORAGE_OSSENDPOINT    | string  | "oss-endpoint"               |

### Storage Options

| Variable                               | Type   | Default Value    |
| -------------------------------------- | ------ | ---------------- |
| DIRECTUS_STORAGE_OPTIONS_ACL           | string | "public-read"    |
| DIRECTUS_STORAGE_OPTIONS_CACHECONTROL  | string | "max-age=604800" |

### Mail

| Variable                         | Type   | Default Value       | Possible Value |
| -------------------------------- | ------ | ------------------- | -------------- |
| DIRECTUS_MAIL_DEFAULT_TRANSPORT  | string | "sendmail"          | "smtp"         |
| DIRECTUS_MAIL_DEFAULT_FROM       | string | "admin@example.com" |                |
| DIRECTUS_MAIL_DEFAULT_HOST       | string | ""                  |                |
| DIRECTUS_MAIL_DEFAULT_PORT       | string | ""                  |                |
| DIRECTUS_MAIL_DEFAULT_USERNAME   | string | ""                  |                |
| DIRECTUS_MAIL_DEFAULT_PASSWORD   | string | ""                  |                |
| DIRECTUS_MAIL_DEFAULT_ENCRYPTION | string | ""                  |                |

### CORS

| Variable                     | Type    | Default Value                                |
| ---------------------------- | ------- | -------------------------------------------- |
| DIRECTUS_CORS_ENABLED        | boolean | true                                         |
| DIRECTUS_CORS_ORIGIN         | array   | ["*"]                                        |
| DIRECTUS_CORS_METHODS        | array   | ["GET","POST","PUT","PATCH","DELETE","HEAD"] |
| DIRECTUS_CORS_HEADERS        | array   | []                                           |
| DIRECTUS_CORS_EXPOSEDHEADERS | array   | []                                           |
| DIRECTUS_CORS_MAXAGE         | integer | null                                         |
| DIRECTUS_CORS_CREDENTIALS    | boolean | false                                        |

### Rate limiter

| Variable                    | Type    | Default Value |
| --------------------------- | ------- | ------------- |
| DIRECTUS_RATELIMIT_ENABLED  | boolean | false         |
| DIRECTUS_RATELIMIT_LIMIT    | integer | 100           |
| DIRECTUS_RATELIMIT_INTERVAL | integer | 60            |
| DIRECTUS_RATELIMIT_ADAPTER  | string  | "redis"       |
| DIRECTUS_RATELIMIT_HOST     | string  | "127.0.0.1"   |
| DIRECTUS_RATELIMIT_PORT     | integer | 6379          |
| DIRECTUS_RATELIMIT_TIMEOUT  | integer | 10            |

### Auth

| Variable                | Type   | Default Value                             |
| ----------------------- | ------ | ----------------------------------------- |
| DIRECTUS_AUTH_SECRETKEY | string | "type-a-secret-authentication-key-string" |
| DIRECTUS_AUTH_PUBLICKEY | string | "type-a-public-authentication-key-string" |

### Auth (OKTA)

| Variable                                        | Type   | Default Value                                       |
| ----------------------------------------------- | ------ | --------------------------------------------------- |
| DIRECTUS_AUTH_SOCIALPROVIDERS_OKTA_CLIENTID     | string | ""                                                  |
| DIRECTUS_AUTH_SOCIALPROVIDERS_OKTA_CLIENTSECRET | string | ""                                                  |
| DIRECTUS_AUTH_SOCIALPROVIDERS_OKTA_BASEURL      | string | "https://dev-000000.oktapreview.com/oauth2/default" |

### Auth (GitHub)

| Variable                                          | Type   | Default Value |
| ------------------------------------------------- | ------ | ------------- |
| DIRECTUS_AUTH_SOCIALPROVIDERS_GITHUB_CLIENTID     | string | ""            |
| DIRECTUS_AUTH_SOCIALPROVIDERS_GITHUB_CLIENTSECRET | string | ""            |

### Auth (Facebook)

| Variable                                               | Type   | Default Value |
| ------------------------------------------------------ | ------ | ------------- |
| DIRECTUS_AUTH_SOCIALPROVIDERS_FACEBOOK_CLIENTID        | string | ""            |
| DIRECTUS_AUTH_SOCIALPROVIDERS_FACEBOOK_CLIENTSECRET    | string | ""            |
| DIRECTUS_AUTH_SOCIALPROVIDERS_FACEBOOK_GRAPHAPIVERSION | string | "v2.8"        |

### Auth (Google)

| Variable                                          | Type    | Default Value |
| ------------------------------------------------- | ------- | ------------- |
| DIRECTUS_AUTH_SOCIALPROVIDERS_GOOGLE_CLIENTID     | string  | ""            |
| DIRECTUS_AUTH_SOCIALPROVIDERS_GOOGLE_CLIENTSECRET | string  | ""            |
| DIRECTUS_AUTH_SOCIALPROVIDERS_GOOGLE_HOSTEDDOMAIN | string  | "*"           |
| DIRECTUS_AUTH_SOCIALPROVIDERS_GOOGLE_USEOIDCMODE  | boolean | true          |

### Auth (Twitter)

| Variable                                         | Type   | Default Value |
| ------------------------------------------------ | ------ | ------------- |
| DIRECTUS_AUTH_SOCIALPROVIDERS_TWITTER_IDENTIFIER | string | ""            |
| DIRECTUS_AUTH_SOCIALPROVIDERS_TWITTER_SECRET     | string | ""            |

## Docker

### General

| Variable                  | Type   | Default Value       |
| ------------------------- | ------ | ------------------- |
| DIRECTUS_USE_ENV          | number | 1                   |

### Installation process

| Variable                  | Type    | Default Value       |
| ------------------------- | ------- | ------------------- |
| DIRECTUS_INSTALL_TITLE    | string  | "Directus"          |
| DIRECTUS_INSTALL_EMAIL    | string  | "admin@example.com" |
| DIRECTUS_INSTALL_PASSWORD | string  | random              |
| DIRECTUS_INSTALL_FORCE    | boolean | false               | 
