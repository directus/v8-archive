# 💬 Troubleshooting

> Below are solutions to some common issues that you may experience when working with Directus. You can also post questions to [StackOverflow](https://stackoverflow.com/questions/tagged/directus) or reach out to the members of our [Slack](https://directus.chat) community!

## Install Checklist

If you're having difficulty getting Directus up-and-running, this setup checklist will help troubleshoot and diagnose issues. Follow each step, in order, and you should be good to go!

:::warning
**This checklist applies to the OS-level requirements for your Directus server**. If you are installing Directus using any of the other installation methods outlined [here](/getting-started/installation.html#setup), you will need to examine your container image (or other system configuration) to ensure these requirements are met.
:::

1. **Does your server meet the minimum requirements**

    - Apache 2
    - PHP 7.2+
    - MySQL 5.7+

2. **Are all the required PHP Extensions enabled?**

    - `pdo`
    - `mysql`
    - `curl`
    - `gd`
    - `fileinfo`
    - `mbstring`
    - `xml`

3. **Is `mod_rewrite` enabled? Do `.htaccess` files work?** — [DigitalOcean Guide](https://www.digitalocean.com/community/tutorials/how-to-set-up-mod_rewrite#Section%202)

4. **Is the root directory of the Directus API set to the `/public` directory?**

5. **Is the directory ownership set to the web server configured user?** — Usually the user is `www-data`. You can change the ownership of a directory (cascading) using this command:

    ```bash
    # sudo permissions required
    #
    # the -R flag indicates recursive execution of this change (all contents within the directory will also be changed)
    #
    # www-data:www-data means the user AND group owners will be set to www-data i.e. <user>:<group>
    #
    # /var/www/api is our example directory
    sudo chown -R www-data:www-data /var/www/directus
    ```

6. **Do the following folders have write permission?**

    - `/logs`
    - `/public/uploads` (or your configured upload directory)

7. **Are you using the [latest release of Directus](https://github.com/directus/directus/releases)?**

8. **Did you install or create the API configuration file manually?**

9. **Did you get `pong` response from the `/server/ping` server endpoint?**

10. **Are you using the correct API project URL?**

11. **Did you check your logs?**

    - Directus Logs
    - Apache Logs
    - PHP Logs
    - MySQL Logs

12. **Did you check [GitHub](https://github.com/directus) for an open issue similar to yours?**

## Frequently Asked Questions

Below are questions that are commonly asked by people working with Directus. If you can't find an answer, you can post questions to [StackOverflow](https://stackoverflow.com/questions/tagged/directus) or reach out to the members of our [Slack](https://directus.chat) community!

### Why is my relational data/file only returning an ID?

Directus allows you to request specific field data at different depths. You can use the [`fields` parameter](../api/query/fields.html) to fetch deeper relationships fields (eg: `?fields=*.*.*` for three levels deep) or even specific fields to keep things as performant as possible (eg: `?fields=image.title` for a relational image's title).

However you'll also want to ensure that you have the correct read permissions for the related tables you're requesting. By default, Directus keeps all data private by default, so you will need to enable read permission for the tables you're trying to access. If you're making _unauthenticated_ requests that include file data, you'll also need to allow read access to `directus_files` for the `public` role. Be aware, this may allow advanced users to list all files.

### Why can't I login to the demo?

You can login to our [online demo](https://demo.directus.io) with `admin@example.com` and `password`. Our demo is public, and so occasionally users will change these credentials (locking everyone else out) or mess up other demo data. If this happens, just wait a bit... our entire demo server resets each hour.

### How can I increase the 2MB limit on file uploads?

Directus uses the file limit configured within your server's PHP environment. To update this limit you'll want to edit `upload_max_filesize` and `post_max_size` within your `php.ini` file.

### Does Directus handle deploying or migrating of content?

Directus does not currently provide any tools for migrating or deploying your schema/data changes between different environments (_ie. from development to production_).

### Which SDKs are available for Directus?

Currently, the only SDK maintained for Directus is the JavaScript SDK, it's difficult to be Open-Source and maintain multiple SDKs manually.

We're hoping for, and awaiting support from the [OpenAPI 3.0 Spec](https://github.com/OAI/OpenAPI-Specification/issues/1706) to autmatically generate any and all other SDKs. Giving a thumbs-up could really help! You can, follow the progress of Directus SDK coverage [here](https://github.com/directus/directus/issues/2255).

When using PHP, we recommend using Guzzle for the time-being and call the [API](/api/reference.html#introduction) directly.

### Why is my MariaDB installation not working?

While we don't officially support MariaDB, many successfully use that database type. Some users have noted seeing the following error message: `not able to install database: ./directus install:database SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes`

When you're using MariaDB, though it's based on the same source code as MySQL, there are subtle differences. Directus requires the use of the `utf8mb4` charset. [MariaDB V10.2](https://mariadb.com/kb/en/library/supported-character-sets-and-collations/) or higher will be required for this to work.

### Why can't I save content with certain languages, characters, or emoji?

This is an issue with the database itself. Please confirm that your database, tables, and fields are all set to use `utf8-mb4` charset and encoding.

## Technical Support

Directus is offered as free and open-source for users who wish to self-host the software. However, with millions of installs around the world, there is no way that our small team can offer free support. Below we've outlined the different support options available.

:::tip
If your issue really seems like a `bug` (not a configuration problem) then please report it on GitHub issues for the [API](https://github.com/directus/api/issues/new?template=Bug_report.md) or [App](https://github.com/directus/app/issues/new?template=Bug_report.md). The more information you can provide, the more likely it is that we can fix the issue quickly.
:::

### Community Support

[StackOverflow](https://stackoverflow.com/search?q=directus) is great first place to reach out for help. Our community and Core developers often check this platform and answer posts tagged with `directus`.

Another option to get help is our [Slack Community](https://directus.chat). Please keep all questions on the `#support` channel, be considerate, and remember that _you are getting free help for a free product_.

### Standard Support

Our Core team offers standard support to all on-demand tier Directus Cloud customers, as well as some strategic partners.

### Premium Support

Premium support is available to the following:

* [Enterprise tier Directus Cloud customers](https://directus.cloud)
* [GitHub Sponsors in our "Retainer" tiers](https://github.com/sponsors/directus)
* [Clients of RANGER Studio (our parent agency)](https://rangerstudio.com/)
* [Self-Hosted users paying for hourly support](mailto:info@directus.io)

::: tip Getting Premium Support
Premium support is billed at a rate of **$125/hour** (standard) or **$187.50/hour** (expedited). If you are interested in acquiring premium support, please contact us at: [info@directus.io](mailto:info@directus.io).
:::
