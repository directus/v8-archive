# Status

> The Status interface adds soft-delete and workflow options to its parent collection. Let's take a look at how to properly configure it to take full advantage of its power and flexibility.

## Status Mapping

There are three status options added by default, however you can add as many as you'd like. Each option has paramenters that define how it will look and work.

Option              | Description
------------------- | -----------------------------------
`key`               | String (Numbers can be used as strings). Each option object is defined by a key, this key is the value that is saved into the database
`name`              | String. The name presented to the user in the App
`text_color`        | Directus Color Name. The text color of the badge on the browse items page
`background_color`  | Directus Color Name. The background color of the badge (or dot if using the Simple Badge option)
`browse_badge`      | Boolean. Whether to show the badge on the browse items page or not
`soft_delete`       | Boolean. If true, items will not be returned by default in the App or API. Only Admin users have access to soft-deleted items

### Example

```json
"published": {
  "name": "Published",
  "text_color": "white",
  "background_color": "accent",
  "browse_badge": false,
  "soft_delete": false
},
"draft": {
  "name": "Draft",
  "text_color": "white",
  "background_color": "blue-grey-200",
  "browse_badge": true,
  "soft_delete": false
},
"deleted": {
  "name": "Deleted",
  "text_color": "white",
  "background_color": "red",
  "browse_badge": true,
  "soft_delete": true
}
```

## Soft Delete

As mentioned above, Soft Delete is meant to _act_ as if an item has been truly deleted, but without permanently removing it from the database. Soft-deleted items are hidden from both the App and API responses unless explicitely requested by an Admin [using the `status` parameter](/api/reference.md#status). Non-admin users do not have access to soft-deleted items.

As of now there is no way to "resurrect" soft-deleted items through the App, they are for all intents and purposes, deleted. However these items still exist in the database and can be acceessed there.

When deleting an item, the API does the following:

1. Check if the collection has a status field
2. Check if the delta data has the status field (meaning the status was changed)
3. Check if the new status value (_from delta data_) has `soft_delete = true`
  * If yes, it sets the `action` to `SOFT_DELETE`
  * If no, it hard deletes the item (permanently removed from the database)
  
::: warning
There may be conflicts when using soft-delete alongside unique columns. This is because soft-deleting is a Directus construct, while `unique` columns are enforced at the database level. Therefore if you soft-delete an item with a _unique_ field, you will get an error if you try to add that value again. The solution would be to remove the unique constraint, or do one of the following:

* Set the `soft_delete` setting to `false` so that it is just another "normal" status option.
* Remove the deleted status option, then simply use item red delete button for hard-deleting.
* Delete the `status` field altogether, users with permission to delete will hard delete items.
:::

## Workflow

The status interface also enables extended permission options that allow [setting permissions based on an item's status](/guides/permissions.md#status-level).

## Custom Status Interfaces

The core status interface should work for 90% of use-cases, but you can still take advantage of the functionality with different styling or interactions. To do this you would [create a custom interface](/extensions/interfaces.md) that uses the `status` [field type](/guides/field-types.md).
