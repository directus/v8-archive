# Interfaces

Interfaces are the way of inputting, editing and viewing data. The most easy way to understand interface is a datepicker. It allows you to represent the date in specified format & you can validate the inputs. It renders the date in better way by providing a diffrent UI than just a normal text.
In Database terms, interface reprents the column's value.

## 💡Interfaces allows you to:

-   Accept & View the data in diffrent formats i.e. Datepicker, Dropdown, Checkboxes.
-   Define the possible choices.
-   Do RegEx validation of data.
-   Define the database schema.

## ⚙️ Default Options

Each interface inherits the below options(passed via `props` and imported via `mixin`)

| Option   | Type              | Default | Desc                                                                                                                                         |
| -------- | ----------------- | ------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| name     | `String`          |         | Represents the column name in database/collection.                                                                                           |
| value    |                   |         | Data stored at respective column.                                                                                                            |
| type     | `String`          |         | Allowed fieldtypes this interface supports. List of all the fieldtypes can be found [here](https://docs.directus.io/guides/field-types.html) |
| length   | `[String,Number]` |         | Database length for the column.                                                                                                              |
| readonly | `Boolean`         | `false` | If the interface is in "Edit" mode or "Display" Mode.                                                                                        |
| required | `Boolean`         | `false` | If the value is reqired.                                                                                                                     |
| options  | `Object`          | `{}`    | Interface specific options which are defined in `meta.json` are available under this.                                                        |
| newItem  | `Boolean`         | `false` | When creting a new item inside collection, this option is set to true for each interface.                                                    |
| relation | `Object`          |         | Defines the relation with other collections.                                                                                                 |
| fields   | `Object`          |         | Contains the list of other columns and its interface configuration from the same collection.                                                 |
| values   | `Object`          |         | Contains the values of other columns from the same collection.                                                                               |

<!-- ## 🚧 Known Issues -->

## ⚡ Enhancements

-   We can provide a prop named `savedValue` or similar which contains a value returned from an API, is immutable & changes only on successful crud. The `value` prop changes on user's interaction. Thus we can diffrentiate between server's state and user's state.
