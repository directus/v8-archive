# Migrations

> How can I install/migrate a project programatically?

Programatically

```php
$config = new \Directus\Core\Config\PhpLoader(__DIR__.'/projects/{project}.php');
$project = $config->getProject('my_project');

$project->migrate();
```

Globally

```
directus migrate --project php://./projects/my_project.php
```
