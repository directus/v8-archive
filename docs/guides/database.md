# Database

> The Database is the container for your entire Directus project. It's kind of like a very powerful spreadsheet.

## Database Mirroring

Instead of creating a proprietary one-size-fits-all data architecture to store your content, Directus "mirrors" whatever is in your actual SQL database. Think of it like a database client (eg: phpMyAdmin, Sequel Pro, etc) only safe and intutiive enough for non-technical users. This makes it an ideal solution for projects that would like:

* A pure/custom database model that fits their needs
* Significant performance optimizations and indexing
* Data transparency, portability, and security
* The full power of SQL queries via direct database access
* To use their existing database schema and content

### What is a relational database?

Directus has been built to support the most common type of relational database: SQL (Structured Query Language). If you're confused by "relational", it just means that you can add an item in the database once, and then relate it to many other items. For example, you could relate a single author to all of their books instead of typing the same author's name into each of their books.

### How Directus works with databases

One of the most unique concepts of Directus is that it aims to be a pure SQL database wrapper. When you create Directus collections, fields, defaults, datatypes... you are actually just creating tables, columns, etc in a custom SQL database. That means you do not need to shoe-horn your project architecture into a predefined CMS schema. **You are in total control of your data, including how it's organized, stored, and optimized.**

More importantly, all the Directus "stuff" such as settings, revisions, preferences, permissions, comments, etc... are all stored in completely separarate tables from your content. This decoupled approach means that you can easily install Directus on top of an existing SQL database to get started. Or, if you ever want to take your data elsewhere, just delete those Directus system tables and your content remains in a pristine SQL database with no hint that Directus was ever there. **You data is always completely pure and portable so you can come and go from Directus at will.**

### Database vs Directus

There are many advantages to wrapping your database with Directus, below we outline several of the most notable:

* **Presentation**
  Engineers love that databases are essentially a grid of raw data. What you see is what you get. But a thin veil of aesthetics never hurt any one... in fact it makes managing data a lot easier in certain cases as we'll see below.
* **Relational Data**
  Working with primary keys is time consuming and it's easy to forget what you're looking at when you're nested 3-4 levels deep. Directus handles all those native relationships, but gives you context about each item you're working on. So you'll see `John Smith – NYC Office (Accounting) ` instead of `64009`.
* **Managing Assets**
  Sure, you can store BLOBs of file data directly in the database, but you typically don't even get a thumbnail preview... just code. And it takes a script/app to get files there in the first place. Directus lets you see all of your files, manage assets in the filesystem, or even save them to the cloud service of your choice. It also has helpful tools for cropping and resizing.
* **Safety**
  It's way too easy to irreversibly damage a raw database. Have you ever accidentally edited a column and lost data? Truncated a table with millions of records? Deleted a whole database? No one should endure the stressful moments of trying to figure out how recent your latest backup is. Directus keeps all item updates (full and delta), lets you hide dangerous features based on the user's proficiency, and gives appropriate warnings for attempted actions. For example, if you want to delete a collection, you'll need to first confirm your intentions by typing the collection name in.
* **Accountability**
  A database is an excellent single-source-of-truth, but it doesn't track edits and store all deltas for a comprehensive revision history. For all updates, Directus knows what was changed, when, and by who — so you have a full history from creation to publish.
* **Permissions**
  Database users have decent CRUD permissions, but lack the granularity of a full-featured system. For example: column read blacklist based on the record's status and the when created by other users within the permission's role. Complex? Yes. But very powerful.
* **Accessibility**
  Directus adds a comprehensive API wrapper to your database that is dynamically based on your custom schema. It also includes many SDKs for specific languages so you can get connected to your data even faster. Oh, and of course you can always connect to the database directly and completely bypass Directus. That's near impossible with other CMS because of the proprietary and complex way that they store your data.

## Creating a Database

### MySQL

Connect to MySQL:

```
$ mysql -h <host> -u <user> -p
```

The command above will ask you for the user password:

```
$ mysql -h localhost -u root -p
Enter password: ****
```

After you successfully log into MySQL, run the `CREATE DATABASE` command:

```
mysql> CREATE DATABASE directus;
Query OK, 1 row affected (0.00 sec)
```
