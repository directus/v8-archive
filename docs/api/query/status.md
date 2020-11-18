# Status

This parameter is useful for filtering items by their status value. It is only used when the collection has a field with the `status` type. The value should be a CSV.

By default all statuses are included except those marked as `soft_delete`. To include statuses marked as `soft_delete`, they should be explicitly requested or an asterisk wildcard (`*`) should be used.

Example:

```
/_/items/projects?status=*
/_/items/projects?status=published,under_review,draft
```