# Hooks

> Directus provides event hooks for all actions performed within the App or API. You can use these hooks to run any arbitrary code when a certain thing in the system happens. You can use this for automated build triggers, webhooks, or other automated systems.

Hooks are created as functions that are triggered when certain events happen, for example:

```php
<?php

return [
  'actions' => [
    'item.create.articles' => function ($data) {
      $content = 'New article was created with the title: ' . $data['title'];
      notify('admin@example.com', 'New Article', $content);
    }
  ]
];
```

## Creating Hooks

To add a hook, you create a PHP file in the `/public/extensions/custom/hooks/` folder, for example `/public/extensions/custom/hooks/my-hook.php`. This file will need to return an array that contains functions for each of the events you'd like to listen to:

```php
<?php

return [
  'actions' => [
    'application.error' => function (Exception $error) {
      // Do something
    }
  ],

  'filters' => [
    'item.create.articles:before' => function (\Directus\Hook\Payload $payload) {
      // Edit $payload before returning it
      return $payload;
    }
  ]
];
```

#### Below is the list of action hooks

| Action Hook                          | Parameters                |
| ------------------------------------ | ------------------------- |
| `item.create:before`                 | `($collectionName,$data)` |
| `item.create`                        | `($collectionName,$data)` |
| `item.create:after`                  | `($collectionName,$data)` |
| `item.create.collection_name:before` | `($data)`                 |
| `item.create.collection_name`        | `($data)`                 |
| `item.create.collection_name:after`  | `($data)`                 |

### Multiple Files

Sometimes, you might want to split up your hook into multiple files in order to make your main Hook smaller, or to introduce external libraries into the Hook. In order to do this, you can create a folder instead of a single PHP file that contains the file you need.

The API will use the file called `hooks.php` as the entry point for your folder-based hook.

```
/public/extensions/custom/hooks/send-email.php

becomes

/public/extensions/custom/hooks/send-email/hooks.php
```

### Modules, Layouts, and Interfaces

If you're creating a custom Module, Layout, or Interface, you might want to add additional custom Hooks in order to process data send or retrieved for the extension. Modules, Interfaces, and Layouts support their own Hooks by adding a `hooks.php` file to the extension folder. The format is the same as a regular Hook.

## Reference

### Action Hooks

Action Hooks execute a piece of code _without_ altering the data being passed through it. For example, an Action Hook might send an email to a user when an new article is created. Below is a listing of actions that fire an event.

| Name                       | Description                                                                                                                                                                                                                               |
| -------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `application.boot`         | Before all endpoints are set. The app object is passed.                                                                                                                                                                                   |
| `application.error`        | An app exception has been thrown. The exception object is passed.                                                                                                                                                                         |
| `auth.request:credentials` | User requested token via credentials. The user object is passed.                                                                                                                                                                          |
| `auth.success`             | User authenticated successfully. The user object is passed.                                                                                                                                                                               |
| `auth.fail`                | User authentication failed. Exception object is passed.                                                                                                                                                                                   |
| `collection.create`        | Collection is created. Collection's name passed. Supports `:before` and `:after` (default)                                                                                                                                                |
| `collection.update`        | Collection is updated. Collection's name passed. Supports `:before` and `:after` (default)                                                                                                                                                |
| `collection.delete`        | Collection is deleted. Collection's name passed. Supports `:before` and `:after` (default)                                                                                                                                                |
| `field.create`             | Field is created. You can also limit to a specific collection with `field.create.[collection-name]`. Collection's name (_When not specific to a collection_), Field's name and new data passed. Supports `:before` and `:after` (default) |
| `field.update`             | Field is updated. You can also limit to a specific collection with `field.update.[collection-name]`. Collection's name (_When not specific to a collection_), Field's name and data passed. Supports `:before` and `:after` (default)     |
| `field.delete`             | Field is deleted. You can also limit to a specific collection with `field.delete.[collection-name]`. Collection's name (_When not specific to a collection_), Field's name passed. Supports `:before` and `:after` (default)              |
| `item.create`              | Item is created. You can also limit to a specific collection using `item.create.[collection-name]`. Item data passed. Supports `:before` and `:after` (default)                                                                           |
| `item.read`                | Item is read. You can also limit to a specific collection using `item.read.[collection-name]`. Item data passed. Supports `:before` and `:after` (default)                                                                                |
| `item.update`              | Item is updated. You can also limit to a specific collection using `item.update.[collection-name]`. Item data passed. Supports `:before` and `:after` (default)                                                                           |
| `item.delete`              | Item is deleted. You can also limit to a specific collection using `item.delete.[collection-name]`. Item data passed. Supports `:before` and `:after` (default)                                                                           |
| `file.save`                | File is saved. File data passed. Supports `:before` and `:after` (default)                                                                                                                                                                |
| `file.delete`              | File is deleted. File data passed. Supports `:before` and `:after` (default)                                                                                                                                                              |

:::tip Before or After Event
By default, the Hooks above occur _after_ an event has happened. You can append `:before` or `:after` to the end to explicitly specify when the Hook should fire.
:::

#### Example

In this example we'll notify an email address every time a new article is created.

```php
<?php

return [
  'actions' => [
    'item.create.articles' => function ($data) {
      $content = 'New article was created with the title: ' . $data['title'];
      notify('admin@example.com', 'New Article', $content);
    }
  ]
];
```

### Filter Hooks

Filter Hooks are similar to Actions but alter the data that passes through it. For example a Filter Hook might set a UUID for a new article before it is stored in the database.

| Name                 | Description                                                                                                                                                                                                 |
| -------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `item.create:before` | Item is created. You can also limit to a specific collection using `item.create.[collection-name]:before`. Supports `:before` (has to be set explicitly)                                                    |
| `item.read`          | Item is read. You can also limit to a specific collection using `item.read.[collection-name]`. Supports `:after` (default)                                                                                  |
| `item.update:before` | Item is updated. You can also limit to a specific collection using `item.update.[collection-name]:before`. Supports `:before` (has to be set explicitly)                                                    |
| `response`           | Before adding the content into the HTTP response body. You can also limit to a specific collection using `response.[collection-name]`.                                                                      |
| `response.[method]`  | Same as `response` but only executes for a specific http method, such as `GET, POST, DELETE, PATCH, PUT, OPTIONS`. You can also limit to a specific collection using `response.[method].[collection-name]`. |

#### Example

In this example we'll generate and set a `uuid` right before every article is created.

```php
<?php

return [
  'filters' => [
    'item.create.articles:before' => function (\Directus\Hook\Payload $payload) {
      $payload->set('uuid', \Directus\generate_uuid4());

      return $payload;
    }
  ]
];
```

#### Useful Methods when working with the Payload

| Name                     | Description                                                        |
| ------------------------ | ------------------------------------------------------------------ |
| `getData()`              | Get the payload data                                               |
| `attribute($key)`        | Get an attribute key. eg: `$payload->attribute('collection_name')` |
| `get($key)`              | Get an element by its key                                          |
| `set($key, $value)`      | Set or update new value into the given key                         |
| `has($key)`              | Check whether or not the payload data has the given key set        |
| `remove($key)`           | Remove an element with the given key                               |
| `isEmpty()`              | Check whether the payload data is empty                            |
| `replace($newDataArray)` | Replace the payload data with a new data array                     |
| `clear()`                | Remove all data from the payload                                   |

:::tip Dot Notation
`get()` and `has()` method can use dot-notation to access child elements. eg: `get('data.email')`.
:::

::: tip Payload Object
`Payload` object is `Arrayable` which means you can interact with the data as an array `$payload['data']['email]`
:::

:::warning System Collections
Directus _system_ collections also trigger filters, remember to omit them in your filter to prevent damage to the system.

```php
<?php

use Directus\Database\Schema\SchemaManager;

$collectionName = $payload->attribute('collection_name');
if (in_array($collectionName, SchemaManager::getSystemCollections())) {
    return $payload;
}
```

:::

## Web Hooks

Directus natively supports webhooks. They can be configured from the Settings panel of the admin app. If those hooks aren't flexible enough, you can use API Hooks to create your own:

Simply include an HTTP POST that includes the desired data within the event. We've included a [disabled example](https://github.com/directus/api/blob/76413ea8f5b3bf7e90f84a76858ab18c7fc0db67/public/extensions/custom/hooks/_webhook/hooks.php) in the codebase to help you get started.

```php
<?php

return [
  'actions' => [
    // Post a web callback when an article is created
    'item.create.articles' => function ($collectionName, array $data) {
      $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://example.com'
      ]);

      $data = [
        'type' => 'post',
        'data' => $data,
      ];

      $response = $client->request('POST', 'alert', [
        'json' => $data
      ]);
    }
  ]
];
```

## Custom Validation

By using Hooks, you can setup your own custom validation. Below is an example how you can use a RegEx to validate a field using custom rules.

::: tip NOTE
NOTE: Make sure to validate using `:before` to make sure to validate _before_ the data is added into the collection.
:::

```php
<?php

return [
  'filters' => [
    'item.create.articles:before' => function (\Directus\Hook\Payload $payload) {
      if ($payload->has('uuid')) {
        throw new \Directus\Exception\UnprocessableEntityException('Users are not allowed to set UUID');
      }

      $title = $payload->get('title');
      if (!$title || strlen($title) < 10) {
        throw new \Directus\Exception\UnprocessableEntityException('Article title is too short. Expecting at least 10 characters.');
      }

      if (preg_match('/[^a-z0-9]/i', $title)) {
        throw new \Directus\Exception\UnprocessableEntityException('Article title has invalid characters. Only alphanumeric characters are allowed');
      }

      $payload->set('uuid', \Directus\generate_uuid4());

      return $payload;
    }
  ]
];
```

## Custom Error Logs

In order to send the API error logs to an external service, you can use a Hook as well:

Example:

```php
<?php

return [
  'actions' => [
    // Post a web callback when an article is created
    'item.create.articles' => function ($collectionName, array $data) {
      $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://example.com'
      ]);

      try {
        $response = $client->request('POST', 'alert', [
          'json' => [
            'type' => 'post',
            'data' => $data,
          ]
        ]);
      } catch (\Exception $e) {
        // use your own logger
        // log_write($e);
        // Or
        $container = \Directus\Application\Application::getInstance();
        // Monolog\Logger instance
        $logger = $container->fromContainer('logger');
        $logger->error($e);
        // beside error there are:
        // debug, info, notice, warning, critical, alert, and emergency methods
      }
    }
  ]
];
```