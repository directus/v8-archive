# Interface: Text Input

As the name suggests, it renders a normal text field with some additional options & formatting.

## ⚙️ Options

Checkout common interface options(passed via `props`) [here.](../README.md)

| Option             | Default | Interface      | Desc                                                                  |
| ------------------ | ------- | -------------- | --------------------------------------------------------------------- |
| placeholder        |         | [text-input]() | Placeholder Text.                                                     |
| trim               | `true`  | [toggle]()     | Trims the blank space from the start & end of the text.               |
| showCharacterCount | `true`  | [toggle]()     | Displays the number of characters written in textbox.                 |
| iconLeft           |         | [icon]()       | Material Icon to display on the left side.                            |
| iconRight          |         | [icon]()       | Material Icon to display on the right side.                           |
| formatValue        | `false` | [toggle]()     | Pretty output by converting the value to title case.                  |
| width              | auto    | [dropdown]()   | Set textbox width. Available choices are "small", "medium" & "large". |

## 🚧 Known Issues

-   `width` option not working.
-   Can not remove icon once selected. The issue is with `icon` interface though.

## ⚡ Enhancements

-   Masking support may be? or a new inteface for masking?
