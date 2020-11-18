# Users

> Directus Users are the individual accounts that let you authenticate into the API and App. Each belongs to a Role which defines its data permissions.

## System vs. Custom

The `directus_users` table is required by the platform for authentication and other system logic, but often times developers want to use/extend it to also use it for their own purposes. As of now, you can only add additional fields to this table directly in the database (not _officially_ supported), but we'll be adding this functionality to the App soon.

## Creating Users

Administrators can create new users by clicking on the "+" button in the top right of the User Directory or by clicking "Add New" within the Users field of _Settings > Roles & Permissions > [Role]_. Each user has the following options:

* **Status** — determines the current state of the user
    * **Draft** — Still being created. Can not authenticate.
    * **Invited** — Has been invited to the system, but hasn't yet accepted.
    * **Active** — This is the normal state of a user.
    * **Suspended** — Inactive state set by admins or when users reach the [Login Attempts Allowed](./admin-guide.html#security)
    * **Deleted** — For accountability reasons (activity relationships), users are only "soft" deleted.
* **First Name** — The user's given name
* **Last Name** — The user's surname
* **Email** — Part of the user's unique credentials for authenticating
* **Email Notifications** — Whether or not a user should receive important notifications over email
* **Password** — Credentials for authenticating, validated against the [Password Policy](./admin-guide.html#security)
* **Role** — Users must belong to one (and only one) role, which defines their access and permissions
* **Company** — An optional field to track the user's office, location, or organization
* **Title** — An optional field to track the user's company title or position
* **Timezone** — Used to determine relative datetimes
* **Locale** — Used to display any available translations in the App
* **Avatar** — A 200x200 image used throughout the App to more easily identify users
* **Theme** — Sets the App's theme. Options include: Light, Dark, and Auto (based on user's system)
* **Enable 2FA** — Turns on Two-Factor Authentication. May be required by the role's [Enforce 2FA](/roles.html#creating-roles)
* **Token** — (Hidden) This is not editable from within the App. See the note below:

::: tip Creating and Editing Static Tokens
Since static tokens don't change/rotate, they are inherently less secure than authenticating with user credentials and JWT. Therefore we do not expose them directly through the Directus App. For now, to set/change a user's static token, you must do so directly in the database at `directus_users.token` (plaintext). [Learn more about Static Tokens](../api/authentication.html#static-token)
:::

## Deleting Users

To delete a user, click the Delete button in the header of the User's Detail page.

::: warning Soft Deleting
For accountability reasons (eg: activity relationships), users are not actually deleted from the database, they are soft-deleted using the status field. That means that if a user with the same email address is added later, they will inherit any previously saved details/activity.
:::
