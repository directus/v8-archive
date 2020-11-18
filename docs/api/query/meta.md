# Metadata

The `meta` parameter is a CSV of metadata fields to include. This parameter supports the wildcard (`*`) to return all metadata fields.

## Options

*   `collection` - The name of the collection
*   `type`
    *   `collection` If it is a collection of items
    *   `item` If it is a single item
*   `result_count` - Number of items returned in this response
*   `total_count` - Total number of items in this collection
*   `status_count` - Number of items per status
*   `filter_count` - Number of items matching the filter query

```
# Here is an example of all meta data enabled
{
    "collection":"movies",
    "type":"collection",
    "result_count":20,
    "total_count":962,
    "filter_count":120,
    "status_count":{
        "deleted":94,
        "draft":90,
        "coming soon":159,
        "published":181
    }
}
```