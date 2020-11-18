# Installing using Git

We'll guide you through setting up a fresh installation of Directus using Git.

[[toc]]

## Step 1: Make sure requirements are met

Directus requires PHP and MySQL to run properly. That means that a wide variety of webservers, operating systems, and versions are able to run Directus. That being said, we can't promise that Directus will run smoothly in every possible combination of environments. Therefore, we only _officially_ support LAMP stacks.

| Software | Minimum version |
|----------|-----------------|
| Linux    | Ubuntu 18.04    |
| Apache   | 2.4             |
| MySQL    | 5.7+            |
| PHP      | 7.2+            |

::: tip PHP Extensions
The following PHP extensions (typically enabled by default) are also required: `pdo`, `mysql`, `curl`, `gd`, `fileinfo`, `mbstring`, and `xml`.
:::

::: tip MariaDB
While not officially supported, users have reported success installing Directus on MariaDB 10.2+.
:::

::: warning HTTPS
You are required to run Directus using HTTPS.
:::

## Step 2: Clone Directus

Run the following command:

```
git clone https://github.com/directus/directus.git /var/www/directus
```

::: tip
The command above installs Directus to `/var/www/directus`. You can install Directus to any folder you like, just make sure to use the correct path in the following steps.
:::

## Step 3: Configure Apache

### Enable mod_rewrite

Run the following command:

```
a2enmod rewrite
```

### Point Apache to the Directus public folder

Make sure the `DocumentRoot` in your Apache configration points to the `public` folder in Directus, located at `/var/www/directus/public`. You can use the following Apache config as a starting point:

```apacheconf
<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/directus/public

        <Directory /var/www/directus/public/>
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        <IfModule mod_dir.c>
            DirectoryIndex index.php index.pl index.cgi index.html index.xhtml index.htm
        </IfModule>
</VirtualHost>
```

On most servers, the default Apache configuration file is located at `/etc/apache2/sites-available/000-default.conf`. If you're planning on hosting multiple projects on this server, we recommend setting up separate Virtual Hosts for each project. To learn more about that, we recommend reading this article: [How To Set Up Apache Virtual Hosts on Ubuntu 16.04](https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-16-04).

::: tip
Don't forget to restart Apache after making any changes to its configuration!
:::

## Step 4: Set read and write permissions

Directus needs write access to the `logs`, `public`, and `config` directories. Make sure that your PHP user is able to read and write to these locations.

On most LAMP stacks, you can change the owner of the files to `www-data` to ensure the correct permissions are met:

```
sudo chown -R www-data:www-data /var/www/directus
```

## Step 5: Setup a database and user in MySQL

While you can technically use the `root` MySQL user for Directus, we strongly recommend creating a separate user that only has access to the database that Directus will use.

To learn how to do this, we recommend the following article: [How To Create a New User and Grant Permissions in MySQL](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql).

## Step 6: Install your first project

Open Directus in the browser. It should take you straight to the installation wizard. If you were following along with the steps above, Directus will be located at `http://<ip-address>/`.

::: warning Troubleshooting
If you run into any issues with the above steps, please follow our [troubleshooting guide](/getting-started/troubleshooting.md).
:::