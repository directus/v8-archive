# 🐰 Welcome to the Directus Docs!

> These Docs will help get you up-and-running quickly, guide you through advanced features, and explain the concepts that make Directus so unique.

## What is Directus?

**Directus is an open-source suite of software that wraps custom SQL databases with a dynamic API and intuitive Admin App.** It allows both administrators and non-technical users to view and manage the content/data stored in pure SQL databases. It can be used as a headless CMS for managing project content, a database client for modeling and viewing raw data, or as customizable WebApp.

:::tip What's in a name?
Directus ([duh REKT iss](http://audio.pronouncekiwi.com/Salli/Directus)) is latin for: _laid straight, arranged in lines_. The broadest goal of Directus is to present data in a simple, orderly, and intuitive way.
:::

### What is a "Headless" CMS?

With _traditional_ CMS you choose a design template, throw in some content, and it generates a blog or simple website. However that means that these platforms are limited to websites only, since your designs and content are all jumbled together.

In a "headless" CMS, there are no templates built-in, no design or layout editor, and it doesn't generate a website (aka the "head"). It only manages _content_. That's it. Now that your content is cleanly decoupled, you can connect it anywhere! Websites, sure, but also native apps, kiosks, digital signage, other software, internet-of-things (IoT) devices, or any other data-driven project.

In short: traditional CMS are for small or medium-sized websites, headless CMS can manage content for absolutely anything.

### What is "Database Mirroring"?

Directus uses the SQL database's schema to dynamically build a set of custom API endpoints based on your custom achitecture. This means you can install it on top of existing databases, tailor actual database tables/columns to specific project requirements, and even build/optimize in the database directly.

Perhaps one of the biggest advantages of using a database-wrapper like ours in your project, is that you always have direct access to your pure and unaltered data. Meaning, you always have the option to bypass our API, SDK, or CMS and connect to your data directly — effectively removing _any_ bottleneck or additional latency.

_Let's take a look at how Directus maps to your database..._

* **[Project](/guides/projects.html)** — A database (and its asset storage)
* **[Collection](/guides/collections.html)** — A database table
* **[Field](/guides/fields.html)** — A database column (including its datatype)
* **[Item](/guides/user-guide.html#items)** — A database record/row

_Additionally, Directus adds several layers for presentation..._

* **[Module](/extensions/modules.html)** — Modular extensions for project sections/pages
* **[Layout](/guides/layouts.html)** — Modular extensions for displaying collections
* **[Interface](/guides/interfaces.html)** — Modular extensions for interacting with fields

## Choosing an Architecture

Directus is a toolkit that provides many different options for how you can organize your projects, properties, environments, and data. Unlike other platforms, there's no _one right way_ to do things. This freedom can be overwhelming to some developers, so let's cover a few high-level ways to structure things:

* **Instances** (installation-level) have the broadest scope. You can use this to define an App, Team, Client, etc. This is useful if you want to have things running on different servers with altogether different API/App URLs, or if you need to customize/extend the actual Directus source code.
* **Projects** (database-level) have their own data model and API, with scoped CMS users, asset storage, extensions, and branding. Directus is multitenent, so you can have multiple public/private projects per instance. Often projects are used as environments (eg: staging, prod), or to separate client projects.
* **Roles** (permission-level) can be used to scope access to Collections and Items (see below).
* **Collections** (table-level) are a good way to separate content within different data models. You can create different collections, and then broadly enable/disable access to them with Role permissions.
* **Items** (record-level) can be scoped based on a Role's permissions. This can limit user access within collections using the same data model (eg: only view/edit items created by users within your role).

## Core Principles

Directus is a simple solution for complex problems. Every aspect of Directus is data-first and guided by the following core principles:

* **Agnostic** — Directus is not specific to websites or limited to HTML. Your data is compatible with any platform or device so you can connect it to all of your projects.
* **Extensible** — Directus can not be outgrown. Every aspect of the toolkit is modular, allowing you to adapt, customize, and extend the Core feature-set.
* **Limitless** — Directus does not impose any arbitrary restrictions or limits. Add as many users, roles, locales, collections, items, or environments as you'd like.
* **Open** — Directus is not a closed, obfuscated, or black-boxed system. Its simple codebase public and transparent so you can audit the data flow from end-to-end.
* **Portable** — Directus does not _lock_ you to its platform or services. You can migrate your data elsewhere at any point — or just delete Directus and connect to your database directly.
* **Pure** — Directus does not alter your data or store it in a predefined or proprietary way. All system data is stored elsewhere, never commingled.
* **Unopinionated** — Directus does not impose any self-proclaimed "best practices". It lets you decide how your data is modeled, managed, and accessed.

## The Directus Ecosystem

There are several properties within the Directus ecosystem, below is a brief overview.

### Directus Suite

This is the actual Directus software you would install on your server. It includes the Directus API, Directus Admin App, and all dependencies.

### Directus API

This is the "engine" of Directus, providing a dynamic API for any MySQL database. It is bundled in the Directus Suite. The [codebase](https://github.com/directus/api) is written primarily in PHP and uses Zend-DB for database abstraction.

### Directus Application

This is the admin app that allows non-technical users to manage database content. It is bundled in the Directus Suite, and requires an instance of the Directus API to function. The [codebase](https://github.com/directus/app) is written in Vue.js.


### Directus Cloud

Directus is completely free and open-source, but we also offer a [Content-as-a-Service platform](https://directus.cloud/) to help offset our operating costs. The open-source and hosted versions are identical, but our Cloud service offers faster setup, automatic upgrades, and premium support.

### Directus Docs

This is what you're reading right now. We believe that maintaining great Documentation is just as important as releasing great software. Luckily our docs are all written in markdown in a [public repository](https://github.com/directus/directus-8-legacy) so the community can help!

### Directus Demos

To make it as easy as possible to actually play around with Directus we maintain an online demo: [demo.directus.io](https://demo.directus.io). The demo resets each hour so if things look a little screwy just wait a bit. The credentials for this demo are: `admin@example.com` and `password`.

### Directus Cloud Status

For up-to-date system information you can check our [Status Page](https://status.directus.io). This page provides current and historical incident details, as well as our current 30-day uptime percentage.

### Directus Website

For general information, resources, and team info we maintain a marketing [website](https://directus.io/). This is a good non-technical hub to serve as as introduction to the Directus platform.

### Directus Community

Join our growing community on [Directus Slack](https://directus.chat) (1,600+ members) to get an inside peak into what's happening within our organization. From community support to seeing where the platform is heading next, it's a great way to get more involved!

### Directus Locales

In addition to managing multilingual content, the Directus Admin App itself can also be translated into different languages. Our locales are managed through the [Directus CrowdIn](https://locales.directus.io/), which provides a friendly interface and automatically submits pull-requests to the git repository.

### Directus Marketplace

Coming in 2020, this will be a library of extensions available for Directus. Eventually we plan on opening this up to community submissions and allowing monetization — but initially it will showcase free extensions created by our core team.

## Concept Glossary

Definitions and other various terms that are exclusive to the Directus Ecosystem.

* **[Alias](/guides/fields.html#alias-fields)** — A field that does not actually map to a database column (eg: a O2M field).
* **Boilerplate** — The base schema and system content included in a fresh/blank copy of Directus.
* **[Bookmarks](/guides/user-guide.html#bookmarking)** — A specific view of a collection scoped to a user, role, or project.
* **Client** — An external application using data managed by Directus (eg: a website, native app, kiosk, etc).
* **[Collection](/guides/collections.html)** — A grouping of similar items. Each collection represents a table in your database.
* **Collection Preset** — See "bookmark".
* **[Database Mirroring](/guides/database.html#database-mirroring)** — Directus pulls its data model dynamically from any custom SQL database.
* **Datatype** — The vendor-specific SQL database storage type (eg: `VARCHAR`, `BIGINT`)
* **Display Template** — A Mustache-style string used to format field values. For example: `{{first_name}} {{last_name}}, {{title}}`
* **Environment** — A flag set in the project config: either `production` or `staging`.
* **[Extension](/extensions/)** — Extend the core codebase, including: Interfaces, Layouts, Modules, Storage Adapters, etc.
* **[Field](/guides/fields.html)** — A specific type of data within a Collection. Fields represent a database column (except aliases).
* **[Field Type](/guides/field-types.html)** — How field data should be stored. These map to database datatypes (eg: `string`, `number`) or other non-standard options that provide additional functionality (eg `o2m`, `translation`).
* **[Headless CMS](/getting-started/introduction.html#what-is-a-headless-cms)** — Only manages content/assets and makes all data accessible via an API. Unlike traditional/monolithic CMS that commingle in a website's presentation via design templates (the "head").
* **Instance** — A single installation of Directus, including the API, App, and one or more database projects.
* **[Interfaces](/guides/interfaces.html)** — Interfaces provide different ways to view or interact with field data. For example, a string field type could have an input, dropdown, radio button, or completely custom/proprietary interface.
* **[Items](/guides/user-guide.html#items)** — A single record of data within a collection. Each item represents a row within the database.
* **[Length](/guides/fields.html#_2-database-options)** — The amount/size of data that can be stored in a database column or Directus field.
* **[Layout](/guides/layouts.html)** — The presentation of data on the Item Browse page. This could be a listing, tiles, calendar, map, chart, or any other way to showcase data.
* **[Multitenancy](/guides/projects.html#multitenancy)** — Each instance of Directus can manage multiple projects. These can be used to organize properties, environments, projects, or anything else.
* **[Note](/guides/fields.html#_2-database-options)** — Descriptive text displayed below a field to help explain, instruct, or give context to App users.
* **[Project](/guides/projects.html)** — Comprised of a database, config file, and asset storage. Each instance of Directus lets you manage multiple projects, which can be used for different clients or environments.
* **Schema** — The SQL database's tables, columns, datatypes, defaults, and other architectual information. This does not include any items or content.
* **[Telemetry](https://github.com/directus/telemetry)** — Directus pings a centralized server to check if an upgrade is available. Telemetry also sends optional anonymous metrics used to calculate Directus install count.
* **Versionless** — The Directus API is "versionless", which means that new releases will only include fixes and improvements, but no deprecations or breaking changes.

-----

<a href="https://www.netlify.com">
  <img src="https://www.netlify.com/img/global/badges/netlify-light.svg"/>
</a>
