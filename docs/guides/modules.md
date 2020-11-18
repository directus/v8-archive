# Modules

> Everything within Directus is grouped into Modules. Each defines a different section, with its own navigation and pages. Directus comes with several system modules preinstalled, such as Collections, the File Library, and the User Diretory.

## Collections

This is the portal to all of your content, and the most important module of Directus. Within this module you can explore your collections of items through customized layouts, and view/manage data from item detail pages. [Learn more about Collections](/guides/user-guide.html#collections)

## File Library

This system module is an annex of all file assets uploaded to the project. You can upload files here directly (not associated with specific items/fields) and view/manage all files and embeds in one place. This module serves as a built-in Digital Asset Management (DAM) system. [Learn more about the File Library](/guides/user-guide.html#file-library)

## User Directory

A system module that showcases all of the Directus Users within the project. This module can serve as a built-in Company Directory. [Learn more about the User Directory](/guides/user-guide.html#user-directory)

::: tip Extending Directus Users
While not officially supported (yet), you can technically add new columns to `directus_users` directly in the database to extend it with new/proprietary fields. You'll also want to manually add new rows to `directus_fields` to setup these interfaces, etc.
:::

::: tip Users vs. Directus Users
Because it comes with auth, permissions, and other features out-of-the-box, many developers want to repurpose Directus Users to manage "users" within their proprietary client application. While this may work in some projects, we recommend creating your own `users` collection to avoid issues.
:::

## Settings

**Only available to administrators**, this more technical module is where you configure your project, its data model, permissions, and more. [Learn more about Settings](/guides/admin-guide.html)

## Custom Modules

Custom modules are a great way to add new sections/features to Directus — even large sub-systems, such as: dashboards, reporting, or point-of-sale systems. [Learn more about the Custom Modules](/extensions/modules.html#files-structure)