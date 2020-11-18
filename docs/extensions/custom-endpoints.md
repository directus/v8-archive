# Custom Endpoints

> In addition to the [core endpoints](../api/reference.md), you can add completely custom endpoints. Custom endpoints are easy to create files that return an array with the endpoint path, method, and handler. All custom endpoints are scoped within `/custom` to avoid conflicts with the Core functionality.

## Creating Custom Endpoints

**Global** endpoints are defined in files within `/public/extension/custom/endpoints` ([link](https://github.com/directus/api/tree/master/public/extensions/custom/endpoints)). You can use any file-names or sub-directories to help keep things organized.

**Extension** endpoints are defined in the `endpoints.php` file within that extension's directory.

The endpoint identification will be the file or directory name.

### Using Files

The following file path: A file stored in `public/extensions/custom/endpoints/example.php` will be accesible in `/custom/example` endpoint.

### Using Directories

The following file path: A file stored in `public/extensions/custom/endpoints/example/endpoints.php` will be accesible in `/custom/example` endpoint. When using directories the entrypoint file is `endpoints.php`.

```php
<?php

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Services\ItemsService;

return [
  // The endpoint path:
  // '' means it is located at: `/custom/<endpoint-id>`
  // '/` means it is located at: `/custom/<endpoint-id>/`
  // 'test' and `/test` means it is located at: `/custom/<endpoint-id>/test
  // if the handler is a Closure or Anonymous function, it's binded to the app container. Which means $this = to the app container.
  '' => [
    'method' => 'GET',
    'handler' => function (Request $request, Response $response) {

       // Get all answers from DB
      $itemsService = new ItemsService($this);

      $params = $request->getQueryParams();
      $programs = $itemsService->findAll('items', $params);


      return $response->withJson([
          'data' => [
              $programs
          ]
      ]);
    }
  ]
];
```

## Nesting Endpoints

Endpoints can also be nested, or grouped, under a parent path name.

```php
<?php

return [
  '/articles' => [
    'group' => true,
    'endpoints' => [
      // `/custom/<endpoint-id>/articles
      '' => [
          'method' => 'GET',
          'handler' => function ($request, $response) {

          }
      ],
      // `/custom/<endpoint-id>/articles/category
      '/category' => [
        'method' => 'GET',
        'handler' => function ($request, $response) {

        }
      ],
      // `/custom/<endpoint-id>/articles/preview
      '/preview' => [
        'method' => 'GET',
        'handler' => function ($request, $response) {

        }
      ]
    ]
  ]
];
```
