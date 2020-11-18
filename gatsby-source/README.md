# Gatsby Source Directus

**:warning: This project is under heavy development, so breaking changes may occur on our road to a stable v1.0.0. Any bug report will be highly appreciated.**

[![CI](https://github.com/directus/gatsby-source/workflows/CI/badge.svg)](https://github.com/directus/gatsby-source/actions?query=workflow%3ACI)

A [Gatsby](https://www.gatsbyjs.org/) source plugin to pull content from a [Directus CMS](https://directus.io/) backend.

Inspired by the [gatsby-source-directus7](https://github.com/Jonesus/gatsby-source-directus7) plugin by [Joonas Palosuo](https://github.com/Jonesus)

- [Features](#features)
- [Installation](#installation)
- [Documentation](#documentation)
- [Known Limitations](#known-limitations)
- [Development](#development)
  - [Linting](#linting)
  - [Testing](#testing)

## Features

- Exposes all custom content types & associated records created in Directus as distinct nodes in the Gatsby GraphQL layer
- Mirrors O2M, M2O, M2M, and "File" relations between content types in Directus in the Graph QL layer
- Downloads files hosted in Directus using [gatsby-source-filesystem](https://github.com/gatsbyjs/gatsby/tree/master/packages/gatsby-source-filesystem) for usage with other [Gatsby transformer plugins](https://www.gatsbyjs.org/plugins/?=gatsby-transformer)

## Installation

Installing the plugin is no different than installing other Gatsby source plugins.

Create a gatsby project using [Gatsby quick start guide](https://www.gatsbyjs.org/docs/quick-start).

Install the plugin:

```sh
# using npm
npm install --save @directus/gatsby-source-directus
```

```sh
# using yarn
yarn add @directus/gatsby-source-directus
```

Configure the plugin by editing your `gatsby-config.js`. See below for details.

## Documentation

- [Configure the plugin](https://github.com/directus/gatsby-source/blob/master/docs/configuration.md)
- [Some common use and examples](https://github.com/directus/gatsby-source/blob/master/docs/usage.md)

## Known Limitations

For the a collection type to exist in the GraphQL layer, there must be at least one record processed by the plugin belonging to the collection.

E.g. if either no records exist for the collection, or they are all filtered by the plugin configuration, that collection will **not** appear in the GraphQL layer, and any attempts to query against it will throw an error.

A workaround for simple fields is described in [issue #38](https://github.com/directus/gatsby-source/issues/38#issuecomment-594772507).

Similarly, for a field defined as a M2O, O2M, M2M, or File(s) relation to exist in the GraphQL schema for a given collection, there must exist **at least one** record that has a non-null value for that field. Attempts to query fields where no relations have been defined in Directus will throw an error.

E.g. if we have a `products` collection in Directus that has a `reviews` field defined as a O2M relation to a `review` collection, if **all** `products` have no `reviews`, the `reviews` field will not be available in the `directusProducts` GraphQL schema, and attempts to execute a `query { directusProducts { reviews { ... } } }` query will throw an error.

We do not currently have a workaround for this case.

## Development

The project is written in TypeScript. You can clone the repo and use the command `yarn dev` to start TypeScript in watch-mode. `yarn build` builds the project.

### Linting

Running the linter is as simple as:

```sh
yarn lint

# Fix formatting
yarn format
```

### Testing

Some simple unit tests can be run using the following command:

```sh
yarn test
```

In order to run e2e tests, we rely on [docker](https://docs.docker.com/install/) and [docker-compose](https://docs.docker.com/compose/) for setting up a Directus API.

Start the docker environment:

```sh
docker-compose up -d
```

Usually wait few seconds before the docker environment is ready to accept requests and you can run e2e tests:

```sh
yarn e2e
```

Please see [docker](https://docs.docker.com/) and [docker-compose](https://docs.docker.com/compose/) documentation websites for more details.
