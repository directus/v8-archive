# Comment

The `comment` parameter is used to add a message to explain why the request is being made. This value is stored in the activity record. This can be either optional, required or forbidden, based on permissions.

This parameter will not work when `activity_skip` is enabled.

#### Examples

If you want to keep track of the reason why a project from the `projects` collection went from `active` to `cancelled`, you can add a comment explaining the reason.

```http
PATCH /_/items/projects/1?comment=Client business closed doors
```

```json
{
  "status": "cancelled"
}
```