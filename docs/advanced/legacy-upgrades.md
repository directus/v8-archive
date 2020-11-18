# Legacy Upgrades

> One does not simply `git pull origin master` to upgrade from Directus 6.

Directus 7 is a major release with significant breaking changes from previous versions. Therefore there is no automated way to migrate your settings and configuration from v6 to v7. However, because Directus stores your content as pure SQL, that data is always portable between versions. Below we will cover the _general_ steps you'd need to take to upgrade from Directus 6 to Directus 7.

## Your Content

Let's start with the good news. Directus stores all of _your_ data (the important stuff) as pure, unaltered SQL data... separate from any system data. So **all** of your content can be 100% migrated over. Some of the specific ways you saved data before might have slightly new formatting/syntax... so please confirm a few of those possibilities below:

* **WYSIWYG** — We're using different interfaces that may format HTML in a slightly different way.
* **Status** — You can still use integer-based statuses, but we now use string-based values by default.
* **Files** — You'll want to make sure files are moved to match the storage path in your v7 config.
* **Custom Extensions** — We've moved from Backbone.js to Vue.js... and therefore previous extensions will no longer work in the new Directus. The good news is that Vue is FAR easier to work with... so you'll have no problem creating new interfaces, layouts, and pages in v7.

## Your API

Decoupling the API from the App was one of the main undertakings of this refactor, and the API's URL structure in v7 is also quite different. We've removed the version number (we're versionless now) and have cleaned up almost all of the endpoint and parameter names to be clear and consistent. Mapping previous names to new ones here would be an enormous task – but all previous features (and way more) exist in the Directus API 2.

## Directus Codebase

It goes without saying that the codebase in this ground-up rewrite is totally different. Do not attempt to pull changes from git to upgrade. Instead, you should effectively create a duplicate project and manually migrate things over based on this guide.

## Directus 6 System Data

### `directus_activity`

While the table name hasn't changed, the v7 fields are quite different and revisions have been decoupled into `directus_revisions`, so there is no realistic way to migrate. After upgrading, your item history will be reset.

### `directus_bookmarks`

Bookmarks have been replaced by `directus_collection_presets`. Again, this schema is significantly different and migrating would be difficult. However in v7 you can now set Global or Role level bookmarks.

### `directus_columns`

This has been replaced by `directus_fields`. Previously all relationships were contained within this column info (now decoupled to `directus_relations`), so there is a significant architectual difference making migrations difficult.

### `directus_files`

This table is fairly similar between versions. Below is a column mapping:

| v6 | v7 | Details |
|---|---|---|
| `id`                | `id` | Same in v7 |
| `status`            | --  | Removed in v7 |
| `name`              | `filename` | Renamed in v7 |
| `title`             | `title` | Same in v7 |
| `location`          | `location` | Same in v7 |
| `caption`           | `description` | Renamed in v7 |
| `type`              | `type` | Same in v7 |
| `charset`           | `charset` | Same in v7 |
| `tags`              | `tags` | Same in v7 |
| `width`             | `width` | Same in v7 |
| `height`            | `height` | Same in v7 |
| `size`              | `filesize` | Renamed in v7 |
| `embed_id`          | `embed` | Renamed in v7 |
| `user`              | `uploaded_by` | Renamed in v7 |
| `date_uploaded`     | `uploaded_on` | Renamed in v7 |
| `storage_adapter`   | `storage` | Renamed in v7 |
| --                  | `duration` | Add this new v7 field: `INT(11)` |
| --                  | `folder` | Add this new v7 field: `INT(11)` |
| --                  | `metadata` | Add this new v7 field: `TEXT` |

:::tip
You'll also want to make sure all files are moved to match the storage path in your v7 api.php config file.
:::

### `directus_groups`

Simple O2M user groups have been replaced by a M2M user roles (`directus_roles` and `directus_user_roles`). Even though these are functionally the same now, we've made this schema change to allow users to have _multiple_ roles (with merged permissions) in the future without a breaking change in the schema.

| v6 | v7 | Details |
|---|---|---|
| `id`                | `id` | Same in v7 |
| `name`              | `name`  | Same in v7 |
| `description`       | `description` | Same in v7
| `restrict_to_ip_whitelist`  | `ip_whitelist` | Renamed in v7
| `nav_override`       | `nav_blacklist` | New name and structure
| --                  | `external_id` | Add this new v7 field: `VARCHAR(255)` |


### `directus_messages`

We felt that Messages were not a true "core" feature, and that they would be better suited within an Extension. Comments (a form of message that *is* still in core) now live inside `directus_activity`. You can remove this table during migration, but will lose an Item Comments.

### `directus_messages_recipients`

Removed.

### `directus_preferences`

Just like bookmarks, preferences in v7 are now stored in `directus_collection_presets`. The data structure here is completely different and we do not recommend trying to migrate these values. The good news is that simply opening a collection in v7 will automatically save new defaults.

### `directus_privileges`

This has been renamed to `directus_permissions` and we've replaced the INTEGER based privilege values with an easier-to-read STRING permissions in v7. While you could try updating columns (see below) we'd recommend re-creating permissions in v7. There are several new permissions allowed to take advantage of, and permissions isn't something you want to mess up in migration.

| v6 | v7 | Details |
|---|---|---|
| `id`                | `id` | Same in v7 |
| `table_name`        | `collection`  | Renamed in v7 |
| `allow_view`        | `read` | Renamed in v7 |
| `allow_add`        | `create` | Renamed in v7 |
| `allow_edit`        | `update` | Renamed in v7 |
| `allow_delete`        | `delete` | Renamed in v7 |
| `allow_alter`        | -- | Removed in v7 |
| `group_id`        | `role` | Renamed in v7 |
| `read_field_blacklist`        | `read_field_blacklist` | Renamed in v7 |
| `write_field_blacklist`        | `write_field_blacklist` | Renamed in v7 |
| `nav_listed`        | -- | Removed in v7 |
| `status_id`        | `status` | Renamed in v7 |
| --        | `comment` | Added in v7 |
| --        | `explain` | Added in v7 |
| --        | `status_blacklist` | Added in v7 |

### `directus_schema_migrations`

Renamed to `directus_migrations`. The data model has changed here, but you should **not** migrate this utility table from previous versions. Please use whatever boilerplate is installed by v7.

### `directus_settings`

These Settings are still simple key-value-pairs, but we've renamed `collection` (meaning "group") to `scope`. Most of these should migrate over without issue, but since there are only a handful of simple Settings in v7 we recommend just using the default v7 table values.

### `directus_tables`

Has been renamed to `directus_collections`. We won't be listing the column differences here, as we wouldn't want anyone to think they should migrate this manually. Simply use the new v7 defaults and keep the following in mind:

* All status options (`default_status`, `status_column`, and `status_mapping`) have been moved into the `status` interface
* `primary_column` has been moved to the `primary key` interface
* `sort_column` has been moved to the `sort` interface
* `user_create_column`, `user_update_column`, `date_create_column`, and `date_update_column` have been moved to interfaces as well (under `user` and `datetime` respectively).
* `preview_url` has been moved to the `preview` interface

Other fields have been removed, but `hidden` and `single` are the main options still available.

### `directus_users`

Same name and basic structure, below are the main differences:

| v6 | v7 | Details |
|---|---|---|
| `id`                | `id` | Same in v7 |
| `status`  | `status`  | Now uses a string-based status
| `first_name`  | `first_name`  | Same in v7
| `last_name`  | `last_name`  | Same in v7
| `email`  | `email`  | Same in v7
| `password`  | `password`  | Same in v7. Confirm that both use bcrypt algorithm
| `token`  | `token`  | Same in v7
| `position`  | `title`  | Renamed in v7
| `email_messages`  | `email_notifications`  | Renamed in v7
| `last_access`  | `last_access_on`  | Renamed in v7
| `last_page`  | `last_page`  | Same in v7
| `group`  | --  | Replaced by M2M roles
| `avatar_file_id`  | `avatar`  | Renamed in v7
| `location`  | `company`  | Renamed in v7
| `language`  | `locale`  | From `en` to `en-US` format
| `timezone`  | `timezone`  | Same as in v7

**All other v6 fields have been removed**
