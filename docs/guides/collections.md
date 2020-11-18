# Collections

> A Collection is a grouping of similar Items. Each collection represents a table in your database.

### Creating Collections
<br>
<video width="100%" autoplay muted controls loop>
  <source src="../img/video/create-collection.mp4" type="video/mp4">
</video>

To create a collection, head over to the _Collections & Fields_ page in the _Admin Settings_. From here, click the "+" button in the top right and enter the details for your collection into the modal. Once created, you are taken to the Collection Detail page where several other settings can be configured.

* **Name** — The actual table name in the database. Automatically sanitized as you type, with letters converted to lowercase, illegal characters removed, and spaces converted to underscores.
* **Status** — Adds a `status` field to the collection. [Learn More](./interfaces.html#status)
* **Sort** — Adds a `sort` field to the collection. [Learn More](./interfaces.html#sort)
* **Owner** — Adds a `owner` field to the collection. [Learn More](./interfaces.html#user-created)
* **Created On** — Adds a `created_by` field to the collection. [Learn More](./interfaces.html#datetime-created)
* **Modified By** — Adds a `modified_by` field to the collection. [Learn More](./interfaces.html#user-updated)
* **Modified On** — Adds a `modified_on` field to the collection. [Learn More](./interfaces.html#datetime-updated)

### Configuring Collections

#### Fields

This is where you would set up the fields within this collection. [Learn More About Fields](./fields.html)

#### Name

The collection name can not currently be changed through the App. This is due to the complexity of maintaining relationships throughout the database and the breaking of existing API endpoints. If you need to change the name of a collection, you can:

  * Use collection translations (see below)
  * Delete the collection and create a new one
  * Manually change all referecnes in the database directly (this is not officially supported or recommended)

#### Note

The collection's note field is for internal use only. It helps your users understand the purpose of each collection.

#### Hidden

Some helper collections are not used directly (eg: junctions) and can be globally hidden. As the name implies, this will only _hide_ the collections. It doesn't restrict access to its data. In order to restrict access to this collection, you can use [Permissions](./permissions.md).

#### Single

In certain schema architectures, you may find it helpful to have a collection that can only contain one item. For example, the "About" or "Settings" of your project might be managed within the fields of a single item (also known as a "singleton"). When enabled, clicking the collection in the navigation will open the Item Detail page directly, skipping the Items Browse page.

#### Translations

Since Directus uses a prettified version of the actual database table name in the App, you may want to override/customize this name, or provide translations for other locales. To create a translation, click on the "Create Translation" button, choose a locale, and type your translation. You can also set a value for the default locale to rename the collection.

::: tip
Translations are only used in the App and don't apply to the API or database table names.
:::

#### Icon

You can assign an icon to each collection. This is used in the navigation sidebar, item browse page, and elsewhere throughout the App.

## Managing Collections

Collections added through Directus are automatically managed, however collections added directly to the database are unmanaged by default. This avoids issues with dynamically created temporary tables or any tables outside the scope of your project. Directus completely ignores any unmanaged collections.

Unmanaged tables are grayed out at the bottom of this listing page, and can be managed by clicking the "Manage" button. You can also click "Don't Manage" to stop managing a collection. No data is deleted when you do this, but this collection and its data will no longer be accessible within the App or API.

## Deleting Collections

Clicking a managed collection within the list will open its detail page. From here you can click the red trash icon button at the top-right. You will only be asked to confirm this action once... then this collection and all of its fields/content/config will be permanently deleted.

::: danger
Deleting collections is one of the most dangerous actions you can perform within Directus. It is an irreversible action that can remove vast amounts of data. Please be absolutely sure this is what you want before proceeding.
:::