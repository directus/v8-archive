# Upgrading

The standard upgrade process is as follows:

1. Navigate (`cd`) to the root diretory of Directus
2. Run `git pull origin master` to get the latest code

::: tip Versionless API
The Directus API is "versionless", which means that new releases will only include fixes and improvements, but no deprecations or breaking changes.
:::

## Manually Upgrading FTP Installs

If you do not have access to git on your server and installed Directus via FTP, then your upgrade process is as follows:

1. Download the [latest release of Directus](https://github.com/directus/directus/releases/latest)
2. Upload/replace existing Directus files **making sure not to replace**:
    * [API](https://github.com/directus/directus/tree/master/config) config files (`api.php`, `api.[project].php`, etc)
    * [File storage directory](https://github.com/directus/directus/tree/master/public/uploads)
    * [Custom extensions](https://github.com/directus/directus/tree/master/public/extensions/custom)
    * [Log files](https://github.com/directus/directus/tree/master/logs)
    * Overrides for [CSS](https://github.com/directus/directus/blob/master/public/admin/style.css) and [Javascript](https://github.com/directus/directus/blob/master/public/admin/script.js)

## How can I find my current version of Directus?

You can see your App version by hovering over the Directus logo at the top left of the Login page. The API and App versions are also included in the response from the [Server Information endpoint](/api/reference#information) (located at `/server/info`).
