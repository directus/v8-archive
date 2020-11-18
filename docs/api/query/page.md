# Page

Using `page` along with `limit` can set the maximum number of items that will be returned grouped by pages.

::: warning
To see metadata, you need to precise the `meta` query param.
Exemple : `?limit=10&page=3&meta=*`
:::

## Examples

```
# Returns a miximum of 10 items skipping the first 2 pages
?limit=10&page=3

# Here is an example of the response with pagination active
{
    "meta": {
        "collection": "movies",
        "type": "collection",
        "result_count": 10,
        "total_count": 3040,
        "filter_count": 63,
        "limit": 10,
        "offset": 20,
        "page": 3,
        "page_count": 7,
        "links": {
            "self": "http://api.directus.com/_/items/movies",
            "current": "http://api.directus.com/_/items/movies?access_token=token&meta=*&filter[keywords][contains]=account&page=2&limit=10",
            "first": "http://api.directus.com/_/items/movies?access_token=token&meta=*&filter[keywords][contains]=account&page=1&limit=10&offset=10",
            "last": "http://api.directus.com/_/items/movies?access_token=token&meta=*&filter[keywords][contains]=account&page=7&limit=10&offset=60",
            "next": "http://api.directus.com/_/items/movies?access_token=token&meta=*&filter[keywords][contains]=account&page=4&limit=10&offset=30",
            "previous": "http://api.directus.com/_/items/movies?access_token=token&meta=*&filter[keywords][contains]=account&page=2&limit=10&offset=10"
        }
    },
    "data": [...]
}
```

::: warning
If sending the `offset` parameter along with the `page` and `limit` parameters, the `page` parameter will be ignored, and the `offset` parameter will be used.
:::
