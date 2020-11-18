# Setup Development Environment using DDEV

[DDEV](https://ddev.readthedocs.io/en/stable/) allows running the API on your machine using docker which saves you from installing and configuring the dependencies.

## API

* Install DDEV on your machine by following the [installation instructions](https://ddev.readthedocs.io/en/stable/#installation) of your operating system.
* Clone the api repo.
```bash
git clone https://github.com/directus/api.git
OR
git clone git@github.com:directus/api.git
```
* Run ddev config in the API folder and set the `Docroot` to `public` and project type to `php`.
```bash
ddev config
```
* Edit `.ddev/config.yml` to use Apache. 
```yml
webserver_type: apache-fpm
```
* Start ddev.
```bash
ddev start
```
* Install composer dependencies (done once).
```bash
ddev exec composer install
```
* Run the following command in order to get the service and tools details, like `phpmyadmin` and database connection.
```bash
ddev describe
```


## App

* Clone the app repo.
```bash
git clone https://github.com/directus/app.git
OR
git clone git@github.com:directus/app.git
```
* Install dependencies (might need admin privileges).
```bash
npm install
```
* Set the API_URL environment variable to point at the local ddev service URL and start the project.
```bash
set API_URL=https://<ddev-project>.ddev.site/
```
```bash
npm run serve
```
* You'll be prompted to create a new project. Use the following database connection. **host**=db, **database**=db, **port**=3306, **user**=db, **password**=db.

That's it. You will login to a freshly created Directus project. 

**Tip**: ddev allows [PHP Step Debugging](https://ddev.readthedocs.io/en/stable/users/step-debugging/). You can enable it temporarily to avoid performance problems. 

```bash
ddev exec enable_xdebug 
OR 
ddev exec disable_xdebug
```
