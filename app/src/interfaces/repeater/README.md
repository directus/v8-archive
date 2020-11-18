# Interface: Repeater

The repeater acts as a nested form which makes it possible to nest data in an Item.
The nested data gets saved into a JSON format.

## ⚙️ Options

Checkout common interface options(passed via `props`) [here.](../README.md)

| Option             | Default  | Interface        | Desc                                                                      |
| ------------------ | -------- | ---------------- | ------------------------------------------------------------------------- |
| dataType           | `Object` | [button-group]() | Saves nested data in a complex Object or just a single value.             |
| fields             |          | [repeater]()     | Defines the fields the nested Elements will have.                         |
| fields.interface   |          |                  | The type of interface the field should be.                                |
| fields.type        |          |                  | The type of value the field should be saved in.                           |
| fields.index       |          |                  | Saves the value not in the Object but as a key for the responding Object. |
| fields.preview     |          |                  | Shows a preview of the field in the Header.                               |

## Examples

Configuration:
```
options: {
  "dataType": "object",
  "fields": {
    "field1": {
      "interface": "text-input",
      "type": "String"
    }
  }
}
```

Output:
```
[
  {
    field1: "Some Text"
  },
  {
    field1: "Some Other Text"
  }
]
```

Configuration:
```
options: {
  "dataType": "value",
  "fields": {
    "field1": {
      "interface": "text-input",
      "type": "String"
    }
  }
}
```

Output:
```
[
  "Some Text",
  "Some Other Text"
]
```

Configuration:
```
options: {
  "dataType": "object",
  "fields": {
    "key": {
      "interface": "text-input",
      "type": "String",
      index: true
    },
    "value": {
      "interface": "text-input",
      "type": "String"
    }
  }
}
```

Output:
```
{
  "Key1": {
    value: "value1"
  },
  "Key2": {
    value: "value2"
  }
}
```

Configuration:
```
options: {
  "dataType": "value",
  "fields": {
    "key": {
      "interface": "text-input",
      "type": "String",
      index: true
    },
    "value": {
      "interface": "text-input",
      "type": "String"
    }
  }
}
```

Output:
```
{
  "Key1": "value1"
  "Key2": "value2"
}
```
