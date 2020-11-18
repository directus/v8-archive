# Roles

> Roles allow for grouping users and controlling their access to data within the App and API. There are also two system roles for configuring administrators and public data access.

## Administrators

Users within this role have full access to all data within the system and are the only ones with access to Project Settings. This system role is automatically included during installation and can not be removed.

## Public Data

This special role defines what data is accessible **without being authenticated**. For privacy reasons, the public role does not have permission to any data by default. This system role is automatically included during installation and can not be removed.

::: tip No Users
You can not assign users to the Public Role, it is only used to configure public access.
:::

## Creating Roles

You can create additional roles to better organize users or limit their access to content. A role can be created by clicking on the "+" button in the top right of _Settings > Roles & Permissions_ and adding a name. Once created, you will be take to the Role Detail page where you can further configure its options.

* **Permissions** — [Learn more about Permissions](./permissions.md)
* **Name** — The name of the role.
* **Description** — An internal note to help administrators understand its purpose.
* **IP Whitelist** — A CSV of IP addresses allowed to connect to the API that can be used to limit user access.
* **Module Listing** — Allows you to override the items displayed in the module sidebar of the App.
* **Collection Listing** — Allows you to override the items displayed in the navigation sidebar of the App.
* **Enforce 2FA** — Forces the role's users to setup 2FA before being able to login. [Learn more about 2FA](./authentication.html#two-factor-authentication-2fa)
* **Users** — A listing of all users within this role.

## Module Listing

The Module Sidebar is the leftmost part of the Directus App. By default it provides links to the following:

* **Project URL** — Can not be removed. Configure the color, logo and URL within [Project Settings](./admin-guide.html#project)
* **Collections** — An internal link to the Collections Page
* **User Directory** — An internal link to the User Directory
* **File Library** — An internal link to the File Library
* **Help & Docs** — An external link that takes you to these Directus Docs.
* **Admin Settings** — Only displayed for administrators.
* **User Profile** — Can not be removed. Provides a logout button on hover.

To override these defaults for a specific Role, click "Add Module" and then enter the following info:

* **Name** — This is shown on hover of the module button
* **Link** — Where the module button navigates to
    * Internal links are relative, eg: `/:project/collections`, where `:project` is your project's name
    * External links are absolute, starting with `http`
* **Icon** — This is the icon that is shown in the module button

## Collection Listing

The Collection Listing is shown below the project chooser in the navigation sidebar. By default, all of the collections a user has access to are shown here in alphabetical order. To override what is visible, change the order, or add labeled groupings, you start by clicking "Add Group" in the Collection Listing and then enter the following info:

* **Group Name** — An optional label for the group. If left blank, the group will only be separated by a line.
* **Collection** — Click "Add Collection" and then select one from the dropdown to add it to the group.

## Deleting Roles

To delete a role, first remove all its users, then click the Delete button in the header of the Role Detail page.
