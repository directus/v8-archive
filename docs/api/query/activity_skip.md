# Activity Skip

The `activity_skip` parameter prevent the activity logging to be saved in the `directus_activity` table. `activity_skip=1` means to ignore the logging any other value means record the activity.

#### Examples

If there's collection used to logs a project specific activity and that happens frequently and you want to avoid this activity from filling the `directus_activity` collection, you use the `activity_skip=1` query parameter to skip saving this activity.

```http
POST /_/items/doors_access_logs?activity_skip=1
```

```json
{
  "door": "D190",
  "user": 1,
  "datetime": "2018-12-19 14:58:21"
}
```