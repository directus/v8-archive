---
pageClass: page-reference
---

# GraphQL

<two-up>

::: slot left
GraphQL provides a complete description of the data in your API, giving you the power to ask for exactly what you need, and nothing more.
:::

<info-box title="Endpoints" slot="right">

```endpoints
  POST /:project/gql
```

</info-box>
</two-up>

---

## Introduction

<two-up>
<template slot="left">

The Directus REST API offers the same powerful and granular options as GraphQL. However, some users may want to use the specific GraphQL specification for requests and responses. For that reason, Directus offers a GraphQL endpoint as a wrapper of the REST API.

::: warning Admin Role Only
Due to the complexity of handling permissions, as of now the GraphQL endpoint can only be used with Admin role accounts. An admin access token needs to be passed in the `access_token` query parameter.
:::

</template>

<template slot="right">

::: warning Limited Support
Currently supports "Queries" only. "Mutations" and "Subscription" support will be added at a later date.
:::

</template>
</two-up>

---

## Arguments

<two-up>
<template slot="left">

Directus' GraphQL endpoint supports the following 3 arguments:

<def-list>

#### limit <def-type>integer</def-type>
A limit on the number of items to be returned.

#### offset <def-type>integer</def-type>
Skip a given amount of items from the start. Used in pagination.

#### filter <def-type>object</def-type>
Allows you to select a specific set of items based on the given conditions. Filters are a flat-object in the following format: `{ field_operator: value }`

</def-list>
</template>
</two-up>

---

## Examples

<two-up>
<template slot="left">

Get 10 items from the `movies` collection, including the result and total count of items:

```graphql
movies(limit: 10) {
  data {
  	name
  }
  meta {
  	result_count
  	total_count
  }
}
```

Get movie with `id` `15`

```graphql
movies(filter: { id_eq: 15 }) {
  data {
  	name
  	director
  }
}
```

Get all products with a rating of 4.5 and up:

```graphql
products(filter: { rating_gte: 4.5 }) {
  data {
  	name
  	sku
  	price
  }
}
```

</template>
</two-up>
