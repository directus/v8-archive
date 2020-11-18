# Filter


### Filtering

Used to search items in a collection that matches the filter's conditions. Filters follow the syntax `filter[<field-name>][<operator>]=<value>`. The `field-name` supports dot-notation to filter on nested relational fields.

#### Filter Operators

| Operator             | Description                            |
| -------------------- | -------------------------------------- |
| `=`, `eq`            | Equal to                               |
| `<>`, `!=`, `neq`    | Not equal to                           |
| `<`, `lt`            | Less than                              |
| `<=`, `lte`          | Less than or equal to                  |
| `>`, `gt`            | Greater than                           |
| `>=`, `gte`          | Greater than or equal to               |
| `in`                 | Exists in one of the values            |
| `nin`                | Not in one of the values               |
| `null`               | It is null                             |
| `nnull`              | It is not null                         |
| `contains`, `like`   | Contains the substring                 |
| `ncontains`, `nlike` | Doesn't contain the substring          |
| `rlike`              | Contains a substring using a wildcard  |
| `nrlike`             | Not contains a substring using a wildcard |
| `between`            | The value is between two values        |
| `nbetween`           | The value is not between two values    |
| `empty`              | The value is empty (null or falsy)     |
| `nempty`             | The value is not empty (null or falsy) |
| `all`                | Contains all given related item's IDs  |
| `has`                | Has one or more related items's IDs    |

##### Filter: Raw Like

The wildcards character for `rlike` and `nrlike` are `%` (percentage) and `_` (underscore).

> From MySQL Docs: https://dev.mysql.com/doc/refman/5.7/en/string-comparison-functions.html#operator_like
> % matches any number of characters, even zero characters.
> _ matches exactly one character.
>
>`JOHN%` will return matches `John`, `Johnson`, `Johnny`, `Johnathan`, etc. \
>`JO%N%` will return the above matches, as well as `Jon`, `Jonny`, `Joan`, `Joanne`, `Jones`, etc. \
>`J_N%` will return `Janice`, `Jane`, `Jones`, `Jinn`, `Jennifer`, `Junior`, etc. \
>`J_N__` will return `Jonas`, `Jenny`, `Janie`, `Jones`, etc.

##### Filter: Relational

You can use dot notation on relational field. Using the same format: `filter[<field-name>][<operator>]=<value>` with the only difference `<field-name>` can reference a field from the related collection.

If you have a `projects` collection with a field named `author` that's related to another collection named `users` you can reference any `users` field using dot notation; Example: `author.<any-users-field>`.

The example below uses the `rlike` filter to get all projects that belongs to users that has a `@directus.io` domain email. In other words ends with `@directus.io`

```
GET /items/projects?filter[author.email][rlike]=%@directus.io
```

::: tip
Make sure the field is a relational field before using the dot-notation, otherwise the API will return an error saying the field cannot be found.
:::

You can reference as many field as possible, as long as they are all relational field, except the last one, it could be either relational or non-relational.

```
GET /items/users?filter[comments.thread.title][like]=Directus
```

In the example above it will returns all users that have comments in a thread that has `Directus` in its title.

There's two filter `has` and `all` that only works on `O2M`-type fields, any other type of fields used will throw an error saying the field cannot be found.

The `all` filter will returns items that contains all IDs passed.

```
GET /items/projects?filter[contributors][all]=1,2,3
```

The example above will return all projects that have the user with ID 1, 2, and 3 as collaborator.

Using `has` will return items with at least that minimum number of related items.

Example of requesting projects with at least one contributor:

```
GET /items/projects?filter[contributors][has]=1
```

Example of requesting projects with at least three contributors:

```
GET /items/projects?filter[contributors][has]=3
```

#### AND vs OR

By default, all chained filters are treated as ANDs, which means _all_ conditions must match. To create an OR combination, you can add the `logical` operator, as shown below:

```
GET /items/projects?filter[category][eq]=development&filter[category][logical]=or&filter[category][like]=design
```

::: tip
In many cases, it makes more sense to use the `in` operator instead of going with the logical-or. For example, the above example can be rewritten as

```
GET /items/projects?filter[category][in]=development,design
```

:::

#### Filtering by Dates and Times

The format for date is `YYYY-MM-DD` and for datetime is `YYYY-MM-DD HH:MM:SS`. This formats translate to `2018-08-29 14:51:22`.

- Year in `4` digits
- Months, days, minutes and seconds in two digits, adding leading zero padding when it's a one digit month
- Hour in 24 hour format

```
# Equals to
GET /items/comments?filter[datetime]=2018-05-21 15:48:03

# Greater than
GET /items/comments?filter[datetime][gt]=2018-05-21 15:48:03

# Greater than or equal to
GET /items/comments?filter[datetime][gte]=2018-05-21 15:48:03

# Less than
GET /items/comments?filter[datetime][lt]=2018-05-21 15:48:03

# Less than or equal to
GET /items/comments?filter[datetime][lte]=2018-05-21 15:48:03

# Between two date
GET /items/comments?filter[datetime][between]=2018-05-21 15:48:03,2018-05-21 15:49:03
```

For `date` and `datetime` type, `now` can be used as value for "current server time".

```
# Equals to
GET /items/comments?filter[datetime]=now

# Greater than
GET /items/comments?filter[datetime][gt]=now

# Between two date
GET /items/comments?filter[datetime][between]=2018-05-21 15:48:03,now
```

When the field belongs to a Directus collection, `now` is converted to a UTC date/datetime.