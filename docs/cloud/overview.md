# Directus Cloud Overview

### Directus Cloud is the easiest way to get started with Directus. It allows you to spin up new projects in seconds, and keeps those project safe and up-to-date. This page will give an overview of the features that make our Cloud platform an ideal choice for your project.

<br>
<br>

## Dashboard Components

* **Accounts** gives you free access to the Directus Cloud Dashboard
* **Teams** allow you to organize, share, and consolidate billing for your projects
* **Projects** are instances of Directus, organized by team, with pricing defined by its tier
* **Tiers** define your project's service fee and are selected based on expected usage

## Easy Installation

Each instance of Directus, called a "project", is comprised of a database, file storage, and a configuration file. You can [Create a New Project](/cloud/user-guide.md#create-a-project) directly from your Cloud Dashboard in a matter of seconds, making it the easiest way to get started with Directus.

Once installation is complete, you can immediately login with your admin credentials to [Access the Project](/cloud/user-guide.md#access-a-project).

## The Full Directus Suite

Cloud projects give you complete access to the entire Directus Suite. We do not artificailly "lock" any core features or limit usage based on your tier†. Every project gets access to the following:

* Unlimited Locales
* Unlimited Roles
* Unlimited Users†
* Unlimited Collections†
* Unlimited Items†
* Unlimited Files
* Unlimited Thumbnails
* Full REST & GraphQL API

_† May incur overage fees based on usage and tier._

::: tip Overages, Technical Limits & Fair Usage
To ensure you always have full read/write access to your data, no hard limits are imposed for Cloud projects. However it is important to note that you may incur overage fees depending on your usage.

All projects are subject to our [Technical Limits Policy](https://directus.io/technical-limits.html) and [Fair Usage Policy](https://directus.io/fair-usage.html).
:::

## Automatic Updates

Our core team is always hard at work improving the Directus Core suite, with new versions being released every week or so. Our Cloud platform automatically keeps your projects up-to-date with the most recent version, so you get access to the latest features, fixes, and optimizations. No more tedious maintenance or running manual database migrations.

::: tip Current Version
You can confirm the version of your projects in the header of the Cloud Dashboard.
:::

## Uptime & Monitoring

Directus Cloud is built upon a robust auto-scaling architecture that ensures your projects are always blazingly fast. We also have 24/7 system monitoring to detect anomalies before they become issues, so we can maximize your service uptime.

::: tip System Status
You can always check our [System Status](https://status.directus.io/) page to check for service issues or verify uptime. You can also use this page to report incidents, browse past issues, or review the 30-day uptime history.
:::

## No Vendor Lock-In

You are never locked-in to our Directus Cloud platform... or even Directus in general. All your data is stored in pure SQL within a database architecture that you define, so you have the freedom of complete data portability. If you would like to discuss moving from self-hosted to Cloud, or vice versa, please [contact our support specialists](https://dashboard.directus.cloud/?chat=open).

## Automatic Backups

Our Cloud platform keeps your data safe, with redundant backups of all data. Project databases are individually backed-up each hour (24h retention), and our entire database server is backed up nightly (31d retention). We also keep off-site backups of all project config files, and project asset storage.

::: tip Requesting a Backup
If you would like access to a project database backup, please contact us via our [chat service](https://dashboard.directus.cloud/?chat=open).
:::

## Storage & CDN

Content Delivery Network

## Teams & Roles

Teams are a great way to keep your projects organized... and they're free to create. [Create a Team](/cloud/user-guide.md#create-a-team) to group projects by client, company, purpose, payment method, or anything else. Once created, you (the owner) can invite any number of other Directus Cloud Accounts to become members (admins).

Teams are also useful because they allow for consolidated billing across multiple projects. You can enter a team's payment method once, and then quickly create projects as needed.

## On-Demand vs Enterprise

**On-demand Tiers** are the easiest way to get started with Directus and include all of the features listed on this page above. There are three on-demand tiers, each covering some of the most typical project sizes:

* **Basic** — Test things out with 10 collections & 5,000 items
* **Standard** — Our most common size, with 25 collections and 10,000 items
* **Professional** — Max out your project with 50 collections and 25,000 items

**Enterprise Tiers** allow the most flexibility, and are custom tailored to your specific needs. If your project requires larger limits or any of the custom options listed below, you can [chat](https://directus.io/?chat=open) or [email](info@directus.io?subject=I'd+like+to+learn+more+about+Directus+Cloud+Enterprise) our sales staff to discuss moving to an Enterprise tier.

* **App and API Whitelabeling**
* **Premium Support & SLAs**
* **Custom Usage Limits**
* **Dedicated Hardware**
* **Remote Database Access**
* **Bulk Discounts**

**Self-Hosting Directus** is an excellent option for organizations that don't allow their data to be stored third-party services or non-profits with no software budget. However self-hosted instances use our open-source license (GPLv3) and therefore:

* **Are responsible for purchasing their own server and asset storage**
* **Are responsible for upgrading and maintaining their own software**
* **Do not get any standard or premium support — only [community support](https://directus.chat)**

## Extensions & Custom Code

It is important to note that while our on-demand Cloud infrastructure is very performant and flexible, you will not have direct access to modify or extend the Directus codebase. This means that you will not be able to take advantage of some of the extensibility options built-in to the Directus Suite, including:

* Database Import / Export
    * You _can_ request full database exports of your Cloud projects, and we'll work with you (within reason) to import data from existing or self-hosted Directus installs.
* Custom Extensions, such as Interfaces
    * Some Enterprise tiers may allow for this
* Custom Admin App Styling (through CSS)
    * Some Enterprise tiers may allow for this