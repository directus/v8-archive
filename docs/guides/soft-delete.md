# Soft Delete

>  A [Collection](/guides/collections.md) with Soft Delete enabled doesn't delete items permanently, it keeps the data in the database flagged as deleted. You enable Soft Delete on a Collection basis by adding the [Status](/guides/status.md) field to it. Items which are soft-deleted stay in the database but aren't visible in the App or API unless explicitly requested by an Admin [using the `status` parameter](/api/reference.md#status). 

## Enable Soft Delete for a Collections

To enable Soft Delete for a Collection you simply add a new [Status](/guides/status.md) field to it. You do so by going to `Settings -> Collection & Fields` and select your Collection. In your Collection add a new field with the Interface type `Status`.


## How Soft Delete work

When you look into the available option-settings of the Status Interface, you see that it has an option called [Status Mapping](/guides/status.md#status-mapping). 

The Status Mapping option allows you to add or change the statuses of your Status field. For example you can add a `deactivated` status as a fourth option or remove or rename an existing one. 

*Status Mapping config of the Status field:*
```JSON
{
    "published": {
        "name": "Published",
        "text_color": "white",
        "background_color": "accent",
        "browse_subdued": false,
        "browse_badge": true,
        "soft_delete": false,
        "published": true
    },
    "draft": {
        "name": "Draft",
        "text_color": "white",
        "background_color": "blue-grey-200",
        "browse_subdued": true,
        "browse_badge": true,
        "soft_delete": false,
        "published": false
    },
    "deleted": {
        "name": "Deleted",
        "text_color": "white",
        "background_color": "red",
        "browse_subdued": true,
        "browse_badge": true,
        "soft_delete": true, 
        "published": false
    }
}
```

When you look at the Status Mapping option `deleted` you see it has a setting `"soft_delete": true`.
You can change the setting to `false` then your Collection does not have soft-delete applied to it anymore in case no other status option has `soft_delete` set to `true`. 

::: warning
When none of the Status Mapping options has `soft_delete` set to `true`, then all delete operations are permanently and will remove your items from the database too! 
:::

So it is important that one of your Status Mapping options of your Status field has `soft_delete` set to `true` if you want Soft Delete to be enabled for your Collection. 


## Fake Soft Deletes for non-admin users

Soft-deleted items are not visible to non-admin users; for non-admin users a soft-deleted item feels like "permanently deleted" even if it still exists in the database. 

But you can help non-admin users to have something similar like a soft-delete option by adding a fourth option to the Status Mapping of your Status field called `disabled` for example.

This way, editors can simply `disable` an item. They will still see it in the App and can change the value later. When you set the setting `published` to `false` for the `disabled` status, then the disabled item won't be accessible through the API without being requested explicitly through the `status` parameter. 


*Example adding 'disabled' to the Mapping config:*
```JSON
{
    "published": {
        // ...
    },
    "draft": {
        // ...
    },
    "disabled": {
        "name": "Disabled",
        "text_color": "white",
        "background_color": "red",
        "browse_subdued": true,
        "browse_badge": true,
        "soft_delete": false, 
        "published": false
    },
    "deleted": {
        // ...
    }
}
```


