# App Admin Guide

> This guide is written for administrators looking to configure Directus. It is meant to cover setup after installation. If you still need to install Directus, you can follow our dedicated section on [Installing Directus](../getting-started/installation.html).

::: tip Administrators Only
Administrators are the only role with access to the Directus project settings, which can be accessed by clicking the "gear" icon in the module sidebar.
:::

## Global Settings

The general settings for your project.

### Project

* **Project Name** – Identifies this project on the login page and project switcher
* **Project URL** – Clicking the top-left logo square takes you here
* **Project Logo** – A 40x40 pixel brand logo. Ideally an SVG or PNG with alpha layer
* **Project Color** – A hex value for the top-left logo background and login background color
* **Project Foreground** – A centered image shown on the login page when the project is selected
* **Project Background** – A full-screen image shown on the login page when the project is selected
* **Project Public Note** – A short markdown note shown on the login page when the project is selected
* **Default Locale** – The default locale for users. Users can individually override this as needed
* **Telemetry** – Toggles the anonymous Directus telemetry information from being sent

### Data

* **Default Limit** – Default item count returned for App and API responses
* **Sort NULL Last** – NULL values are sorted last

### Security

* **Auto Sign Out** – The number of minutes before an idle user is logged out
* **Login Attempts Allowed** – The number of failed login attempts before a user's status is set to `suspended`. Once suspended, the user can not log in and an administrator must unlock the account.
* **Password Policy** – Saves a RegEx new user passwords must match
    * **None**: No rules are enforced
    * **Weak**: Minimum length 8 characters
    * **Strong**: Minimum length 8 characters with at least 1 lowercase letter, 1 capital letter, 1 digit, 1 special character

::: warning Suspended Administrators
If your project only has one administrator and they become suspended after too many failed login attempts, the only way to re-activate them is directly through the database.
:::

-----

* [Learn more about Authentication](./authentication.html)

### Files & Thumbnails

* **File Naming** – The file-system naming convention used for uploads
    * **UUID (Obfuscated)**: A universally unique identifier that helps keep files private
    * **File Name (Readable)**: A human readable name based on the original upload's title
* **File MimeType Whitelist** — The mimetypes allowed to be uploaded. If empty, all types are allowed.
* **Asset Whitelist** — Defines the details of image assets that can be dynamically generated. [Learn more](./files.md#whitelisting-thumbnails).
* **YouTube API Key** — When added, this allows Directus to fetch more metadata for YouTube embeds.

-----

* [Learn more about Files & Thumbnails](./files.html)

## Collections & Fields

Build your custom data model by creating and configuring Collections, Fields, and Interfaces.

* [Learn more about Collections](./collections.html)
* [Learn more about Fields](./fields.html)
* [Learn more about Interfaces](./interfaces.html)

## Roles & Permissions

Create User Roles, set their permissions, and configure other options. This is also where you manage the Admin and Public roles.

* [Learn more about Roles](./roles.html)
* [Learn more about Permissions](./permissions.html)
* [Learn more about Users](./users.html)

## WebHooks

Create and manage your project's webhooks.

## Activity Log

Provides an overview of system activity for accountability purposes.

* [Learn more about Accountability](./accountability.html#activity)

## Other Cards

There are a few other helpful cards on the Settings page, including:

* **About Directus** – An external link to the Directus marketing site
* **Report Issue** – An external link to the Directus GitHub Bug Report issue template
* **Request Feature** – An external link to the Directus GitHub Feature Request issue template
* **Connection** – _Coming soon._ This will show a history of your API connection latency.
* **Server Details** – _Coming soon._ This will show details of your server and installation.
* **Version & Updates** – _Coming soon._ This will show your current install within a timeline of all versions.