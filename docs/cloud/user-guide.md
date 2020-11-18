# Cloud User Guide

### This guide provides an overview of the Directus Cloud Dashboard and explains how to use each of its features. If you have any other questions along the way, feel free to reach out to our support staff using the chat window of the [Cloud Dashboard](https://dashboard.directus.cloud/?chat=open).

<br>
<br>

## Create an Account

Directus Cloud Accounts are how individual users access the platform. Creating a new account is easy and free, just go to our [Register Account](https://dashboard.directus.cloud/register) page and follow the prompts. If you forgot your password or pre-registered an account, you can go to the [Password Reset](https://dashboard.directus.cloud/request-password) page to create a new password.

## Sign In to your Account

To login, navigate to our [Sign In](https://dashboard.directus.cloud/login) page and enter your account credentials. If you forgot your account's password, you can go to our [Password Reset](https://dashboard.directus.cloud/request-password) page.

## Update your Account

Once logged in to your Cloud Dashboard, click the User Menu (top-right) to open your account detail, then click on _Settings_. From here, you can change your name or password. As of now you can not change your account's email address.

::: tip Pending Invites
The _Invites_ tab lets you review or accept/decline any pending team invitations you may have. You will be notified via email for all new pending team invites.
:::

::: tip Account History
The _Activity_ tab lets you review a 30-day history of all actions performed by your Cloud Account.
:::

## Destroy your Account

**This action is permanent and irreversible.** Destroying your Directus Cloud Account completely removes all your data and assets from our platform. To destroy your Directus Cloud account, follow these steps:

1. Log in to the Cloud Dashboard using the account you would like to destroy
2. Open the User Menu (top-right) and select "Account Settings"
3. Confirm your password, and then click the "Destroy" button

::: warning
You can not destroy your account if you are the owner of one or more Teams. Please transfer ownership or destroy any teams before destroying your account.
:::

## Create a Team

Teams are how you organize Projects and share them across multiple accounts — they're free, so create as many as you'd like. To create a new team, simply click the "Create Team" button on your Dashboard, and enter a Team Name when prompted. You will automatically become the Owner of any teams you create.

::: tip New Team Credit
New teams are given a courtesy credit of **$29 USD** so that you can get started with a Basic tier project for a month before billing starts. **Note: You are still responsible for any overages incurred.**
:::

## Manage a Team

Once logged in to the Cloud Dashboard, click any Team to open its detail page. Depending on your role within the team, you will see different options:

### Owners

* **Create Projects** — Can create new Directus Projects
* **Manage Projects** — Can view, manage, resize, and destroy projects within the team
* **View Billing** — Can view the team balance, credit, payment history, and invoices
* **Add Payment Methods** — Can add new credit cards and change the default
* **Manage Members** — Can invite/remove other members, or transfer ownership
* **Rename Team** — Can change the team name (names are for presentation only)
* **Destroy Team** — Can permanently destroy the entire team

### Admins

* **Create Projects** — Can create new Directus Projects
* **Manage Projects** — Can view, manage, resize, and destroy projects within the team
* **Browse Members** — Can view the other members of the team
* **Leave Team** — Can leave the team ([Learn More](#leaving-a-team))

## Invite a Team Member

As a team's owner, you have the ability to invite other Directus Cloud accounts to become admin members of your team. To do this, follow these steps:

1. Log in to the Cloud Dashboard
2. Click on the Team's header and go to the "Settings" tab
3. Enter an email address and then click the "Send Invite" button

## Remove a Team Member

As a team's owner, you have the ability to remove other admin members from your team. To do this, follow these steps:

1. Log in to the Cloud Dashboard using the _owner_ account of the Team
2. Click on the Team name and go to the "Members" tab
3. Select "Remove" from the user's context menu (three dots on the right-side)

## Change Team Ownership

If you create a team, you are its owner. A team can only have one owner at a time, but you can transfer ownership to any other team members by following these steps:

1. Log in to the Cloud Dashboard using the _owner_ account of the Team
2. Click on the Team name and go to the "Members" tab
3. Select "Make Owner" from the user's context menu (three dots on the right-side)
4. The new owner will get an email notifying them of the change

## Leave a Team

You can leave a team if you no longer want access, but you can not get access again without being re-invited by its owner. To leave a team, follow these steps:

1. Log in to the Cloud Dashboard
2. Click on the Team's header and go to the "Settings" tab
3. Confirm the team name and then click the "Leave Team" button

::: tip Owners vs Admins
You can not leave a team if your account is its owner. You must either transfer ownership or destroy the team.
:::

## Destroy a Team

**This action is permanent and irreversible.** Destroying a Directus Cloud Team completely removes all its data from our platform (for all team members). To destroy a Directus Cloud Team, follow these steps:

1. Log in to the Cloud Dashboard using the _owner_ account of the Team you would like to destroy
2. Click on the Team name and go to the "Settings" tab
3. Confirm the team name and then click the "Destroy Team" button

::: warning
You can not destroy a Team if it contains one or more Projects. Please transfer ownership or destroy any Projects before destroying your Team.
:::

## Create a Project

Cloud projects are on-demand instances of Directus. Each project is created within a team.

1. Click "Create New Project" within the appropriate team
2. Choose a name for the project (names are for presentation only)
3. Set an _Admin Password_ for your project's first admin user
4. Choose a plan based on your content needs
5. If the team doesn't have a payment method on file, you'll be prompted for credit card details

::: tip Credit Card Required
Projects can only be created for teams with a valid payment method on file, even if your team has available credit. Project billing will only begin once its team's credit has been exhausted.
:::

## Manage a Project

Once logged in to the Cloud Dashboard, click any Project to open its detail page. Below we'll cover the different features available within this section:

* **Access** — Contains a large button for accessing Directus itself, a link to the API URL, and a listing of all API Access Tokens. You can also clear or re-generate API access tokens from here.
* **Usage** — Shows the billing period, and the following usage metrics:
  * *API Requests* — Total API request count for this billing period
  * *Bandwidth* — Total API bandwidth for this billing period
  * *Users* — Total number of users in this project
  * *Collections* — Total number of collections in this project
  * *Items* — Total number of items in this project
* **Resize** — Allows you to resize the project tier. Changes are immediate and prorated.
* **Settings** — Lets you rename the project (presentation only) or destroy the project.

## Access a Project

Once logged in to the Cloud Dashboard, click any Project to open its detail page, then go to the "Access" tab. From here you will have access to a large "Launch Directus" button for the Admin App, and a field containing the API's URL.

#### The Admin App

You can copy the URL of the "Launch Directus" to share it with anyone who needs direct access. Below is the format of Cloud Project URLs.

```
https://api.directus.cloud/admin/#/login?project=[cloud-project-id]
```

#### The API

The URL below shows the format of the Cloud Project API URL, and gives a few examples of usage for interacting with endpoints.

```
https://api.directus.cloud/[cloud-project-id]/
https://api.directus.cloud/[cloud-project-id]/collections
https://api.directus.cloud/[cloud-project-id]/items/articles/1
```

<br>

::: tip Your Cloud Project ID
Your Directus Cloud Project ID (eg: `dcpNLHMDvBRfgKbD`) does not change, even when renaming your project. This universally unique identifier for your project is a 16 character hash starting with `dc`.
:::

## Destroy a Project

**This action is permanent and irreversible.** Destroying a Directus Cloud Project completely removes all its data, files, and users from our platform. To destroy a Directus Cloud Team, follow these steps:

1. Log in to the Cloud Dashboard using a member account of the Project you would like to destroy
2. Click on the Project and go to the "Settings" tab
3. **Ensure you've read all warnings and understand exactly what is being deleted**
4. Confirm the project name and then click the "Destroy Project" button

::: danger
This action will break any external apps connecting to the project API or linking to project files. This action is instantaneous and can not be undone. Directus Cloud is not responsible for data, files, or users lost due to this action. **Proceed with extreme caution.**
:::