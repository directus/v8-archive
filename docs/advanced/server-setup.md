# Server Setup

> While only Apache is _officially_ supported, to get Directus working on most servers all you need to do is ensure that traffic is routed to the correct files. Let's take a look at some common examples below.

## Apache

### mod_rewrite

The [`mod_rewrite`](https://httpd.apache.org/docs/current/mod/mod_rewrite.html) is an Apache module that uses a ruled-based rewriting engine to rewrite requested URLs.

Directus API requires `mod_rewrite` to be enabled on Apache, because it uses the URL rewriting to maps all requested URLs to an internal endpoint unless it matches an actual file in the filesystem.

The rewrite rules are include in Directus API inside the `public` directory in a `.htaccess` file that serve as the front controller for all the endpoints.

#### Install mod_rewrite

Apache include `mod_rewrite` by default. If that's not the case, how to install it will depends on your system and apache version, and the best option will be to go to the [Compiling and Installing](http://httpd.apache.org/docs/trunk/en/install.html) section on Apache and tries to compile and install `mod_rewrite` individually.

Apache has a tool called [`apxs`](https://httpd.apache.org/docs/2.4/programs/apxs.html) (APache eXtenSion) for lets you build and install modules this is a good option to install new modules from source.

#### Enable mod_rewrite

There's different way to enable a module after being installed. On ubuntu-based distribution can be enabled using `a2enmod` script.

```
a2enmod rewrite
```

Make sure to reload all apache configuration.

```
service apache2 reload
```

If you are not using a ubuntu-based distribution or `a2enmod` is not available in your system, you can go to your apache configuration, on ubuntu-based system are usually located in `/etc/apache2/conf/httpd.conf`, and add a line to load the rewrite module.

```
LoadModule rewrite_module modules/mod_rewrite.so
```

`rewrite_module` is the module name and `modules/mod_rewrite.so` is the path where the module file is located. In this case the module file is relative to the `ServerRoot` configured in your `httpd.conf`

#### Check if mod_rewrite is enabled

Using the command line you can execute: `apachectl -M | grep 'rewrite'` and it will filter all installed modules that matches `rewrite`, if `rewrite_module` is returned, congratulations you already have installed and enabled `mod_rewrite` in your system.

### AllowOverride

Directus API comes with `.htaccess` files for the required configuration. These `.htaccess` won't work until the `AllowOverride` directive is set within a Directory block.

1. Go to your Apache virtual host configuration
2. Create a `<Directory>` block that points to Directus API root
3. Add `AllowOverride All` inside the `<Directory>` block to allow all directives in `.htaccess` including the `mod_rewrite` directives.

::: tip
Directus `.htaccess` actually uses `FileInfo` for rewriting and `Options` to following symlinks
:::

### Example

```
<VirtualHost *:80>
    ServerAdmin admin@example.com
    ServerName example.com
    ServerAlias www.example.com
    DocumentRoot /var/www/directus/public
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/directus>
      AllowOverride All
    </Directory>
</VirtualHost>
```

::: tip
`.htaccess` is the default filename for the `AccessFileName` directive.
:::

## NGINX

### `directus.conf`

```
server {
    listen 80;
    
    # Change to corresponding location
    root /projects/directus/public;

    # Change virtual host if needed
    server_name directus.local;
    
    index index.html index.htm index.nginx-debian.html index.php;
    
    # Set max body size to allow large files upload, don't forget the change the php-fpm post_max_size setting as well.
    client_max_body_size 256M;

    # disable_symlinks if_not_owner;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location /admin {
        try_files $uri $uri/ /admin/index.html?$args;
    }

    location /thumbnail {
        try_files $uri $uri/ /thumbnail/index.php?$args;
    }

    # Deny direct access to php files in extensions
    location /extensions/.+\.php$ {
        deny all;
    }

    # All uploads files (originals) cached for a year
    location ~* /uploads/([^/]+)/originals/(.*) {
        add_header Cache-Control "max-age=31536000";
    }

    # Serve php, html and cgi files as text file
    location ~* /uploads/.*\.(php|phps|php5|htm|shtml|xhtml|cgi.+)?$ {
        add_header Content-Type text/plain;
    }

    # Deny access to any file starting with .ht,
    # including .htaccess and .htpasswd
    location ~ /\.ht {
        deny all;
    }

    # pass PHP scripts to FastCGI server
    #
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
    
        # With php-fpm (or other unix sockets):
        # fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        # With php-cgi (or other tcp sockets):
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param   SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

}
```

## Caddy

Coming soon.
