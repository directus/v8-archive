# Field Types

> To support multiple SQL vendors (MySQL, PostgreSQL, MS-SQL, etc) Directus has a generalized set of field types to define API output (`array`, `json`, `boolean`, etc) and system data (`m2o`, `alias`, `group`, etc).

* `alias` – Fields that do not have corresponding column in the database
* `array` – Standard array format in API response
* `binary` – Binary strings limited by its length
* `boolean` – `true` or `false`
* `datetime` – A date and time in ISO format, eg: `2018-09-19T14:00:43+00:00`
* `date` – Date, eg: `2018-09-19`
* `time` – Time, eg: `14:09:22`
* `file` – Foreign key to `directus_files.id`
* `group` – Groups fields together visually, children save group into `directus_fields.group`
* `hash` – Its value gets hashed based on the `hasher` option. (Default to: [`password_hash.PASSWORD_DEFAULT`](http://php.net/manual/en/function.password-hash.php))
* `integer` – Whole number
* `decimal` – Number that includes a decimal
* `json` – Standard JSON format in API response
* `lang` – Specific to translation interfaces, this stores the language key
* `m2o` – Many-to-One Relationship
* `o2m` – One-to-Many Relationship
* `slug` – Removes all the special characters from another field. It will ignore the mirrored field value if the slug already has a value set.
* `sort` – System field that stores a item order within the collection items
* `status` – System field used for publishing workflows
* `string` – Any text or characters, limited by its length
* `translation` – Specific to translation interfaces, this is a `o2m` for multi-lingual content
* `uuid` – A Universally Unique Identifier
* `datetime_created` – System field to track the datetime an item was created, used by revisions
* `datetime_updated` – System field to track the datetime an item was updated, used by revisions
* `user_created` – System field to track the user who created an item, used by revisions
* `user_updated` – System field to track the user who updated an item, used by revisions

::: tip Datatypes
When creating or updating fields you can also set a vendor-specific datatype for more granular DBA control over data storage.
:::