# Storage Adapters

> Storage Adapters allow you to save Directus files anywhere. The default storage adapter is the API server's filesystem, but you can also use the included adapter for AWS-S3 or create your own.

## Core Adapters

* **Filesystem** – Store files on the API server's filesystem
* **AWS S3** – Store files on Amazon's Simple Storage Service
* **Rackconnect** – Store files on Rackspace's storage solution

:::tip
You can change your project's default storage adapter within the [API configuration](../advanced/api/configuration.html#storage).
:::

## Using AWS S3

First you need to install the adapter (not installed by default, to keep dependencies small).

::: tip
If are using the Directus Suite version, this version is missing the `composer.json` file. [Follow these steps](https://github.com/directus/api/issues/620#issuecomment-449905619) before installing the AWS S3 storage to recreate `composer.json` and update the composer autoloader. If there's already a `composer.json`, run [`composer dump-autoload`](https://getcomposer.org/doc/03-cli.md#dump-autoload-dumpautoload-).
:::

`composer require league/flysystem-aws-s3-v3`

Then simply set the following into your configuration file (by default `config/<project-name>.php`):

```php
'storage' => [
    'adapter' => 's3',
    'key' => 'your-aws-key',
    'secret' => 'your-aws-secret',
    'bucket' => 'your-aws-bucket-name',
    'version' => 'latest',
    'region' => 'us-east-1',
    'options' => [
      'ACL' => 'public-read',
      'CacheControl' => 'max-age=604800'
    ],
    'endpoint' => 's3-endpoint || http://s3-<region>.amazonaws.com',
    'root' => '/',
    'thumb_root' => '/thumbnails',
    'root_url' => '/',
]
```

- The `root` option indicates the root path of all uploads.
- The `thumb_root` option indicates the root path where all the thumbnails are going to be stored.
- The `root_url` is the URL to access all the files stored in the bucket, such as your bucket url, or AWS CloudFront URL.
- The `endpoint` is the url which s3 is accessed usually in the format of `http://s3-<region>.amazonaws.com`
- The `key` and `secret` is created by IAM access keys

Read more  about the ACL permissions here: https://docs.aws.amazon.com/AmazonS3/latest/dev/acl-overview.html#canned-acl.

If you want to set any new uploaded files to be publicly readable, you should set the `ACL` to `public-read`, otherwise all these permission will follow you default S3 bucket permissions.

## Using AWS S3 with custom endpoint

If you want to connect a custom AWS S3 endpoint (for example a minio server), you can (additionally to the the configuration above) add:

```php
'storage' => [
  'adapter' => 's3',
  // ...
  'endpoint' => 'http://minio.acme.com'
]
```

## Files & Structure

TK
