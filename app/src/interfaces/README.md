# Interfaces

Interfaces are the way of inputting, editing and viewing data. The most easy way to understand interface is a datepicker. It allows you to represent the date in specified format & you can validate the inputs. It renders the date in better way by providing a different UI than just a normal text.
In Database terms, interface represents the column's value.

## 💡Interfaces allows you to:

- Accept & View the data in different formats i.e. Datepicker, Dropdown, Checkboxes.
- Define the possible choices.
- Do RegEx validation of data.
- Define the database schema.

## ⚙️ Settings

| Option           | Desc                                                                                                                                                  |
| ---------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- |
| <br>**Schema**   |                                                                                                                                                       |
| Name             | Represents the column name in database/collection.                                                                                                    |
| Display Name     | Formatted Name to display everywhere inside app. It is generated automatically based on **Name** & is readonly.                                       |
| Note             | For displaying a helping note under the interface.                                                                                                    |
| Field Type       | Allowed fieldtypes for the interface. List of all the fieldtypes can be found [here](https://docs.directus.io/guides/field-types.html)                |
| MySQL Datatype   | For some interfaces, you can explicitly define the MySQL datatypes.                                                                                   |
| Default          | The default value of interface.                                                                                                                       |
| Length           |                                                                                                                                                       |
| Validation       | Provide the RegEx validation rule to validate against.                                                                                                |
| Required         | Enable if the value is required.                                                                                                                       |
| Readonly         | If the value is editable when the interface is rendered. This makes the interface readonly and the value always will be defined in **Default** option. |
| Unique           | Forces the value to be unique in the table for respective column.                                                                                     |
| Hidden On Detail | Hides the interface from the item page while creating or editing an item.                                                                             |
| Hidden On Browse | ??                                                                                                                                                    |
| <br>**Relation** |                                                                                                                                                       |
| Relation         | Defines the relation with other collections.                                                                                                          |
| <br>**Options**  |                                                                                                                                                       |
| Options          | Interface specific options which are defined in `meta.json` are available under this.                                                                 |

## 🎛️ Props

Each interface inherits some props by default.
The following props are derived from the Settings configuration.

| Key      | Type              | Derived From Setting |
| -------- | ----------------- | -------------------- |
| name     | `String`          | Name                 |
| type     | `String`          | Field Type           |
| length   | `[String,Number]` | Length               |
| readonly | `Boolean`         | Readonly             |
| required | `Boolean`         | Required             |
| relation | `Object`          | Relation             |
| options  | `Object`          | Options              |

### Additional Props:

| Key      | Type      | Default | Desc                                                                                         |
| -------- | --------- | ------- | -------------------------------------------------------------------------------------------- |
| value    |           |         | Data stored at respective column.                                                            |
| newItem  | `Boolean` | `false` | When creting a new item inside collection, this option is set to true.                       |
| relation | `Object`  |         | Defines the relation with other collections.                                                 |
| fields   | `Object`  |         | Contains the list of other columns and its interface configuration from the same collection. |
| values   | `Object`  |         | Contains the values of other columns from the same collection.                               |

<!-- ## 🚧 Known Issues -->

## ⚡ Enhancements

- We can provide a prop named `savedValue` or similar which contains a value returned from an API, is immutable & changes only on successful crud. The `value` prop changes on user's interaction. Thus we can differentiate between server's state and user's state.
- The **Default** option interface should render the interface itself. This will enforce the valid default input.
