# Interfaces

> Interfaces determine how a field's value is presented or edited. For example, a _boolean_ value might use a Toggle, Switch, Checkbox, Pill-Box, Icon, or even a Dropdown interface.

## Choosing an Interface

To get started, go to _Settings > Collections & Fields_, choose the Collection you want to add the field to, then click "New Field". The first choice you'll make is what type of interface you want to use — which then determines/limits the subsequent setup options.

## Field Types

Each interface works with one or more field types. For example, the Calendar interface works with the `date` type but not `boolean`. The Text Input is used as a fallback since it works with most field types.

## Interface Options

Interfaces are highly customizable with options that allow you to tailor them to individual uses. These vary depending on interface complexity, with less-common options hidden within an "Advanced" accordion.

## Custom Interfaces

If you need to tailor an interface to your specific needs, or want something completely different, you can create custom interface extensions. [Learn more about that here](/extensions/interfaces.md).

## Core Interfaces

There are a number of interfaces that ship with Directus that cover most use cases out-of-the-box. Below we'll give an overview of each.

### `button-group`
<img src="../img/interfaces/button-group.png">
Selects a single option from a set of options. Options can be presented as text, icons, or both, and can be separated into different groups. Stores value as a `string`.

* `choices` — JSON to define options (label/value) and their grouping
* `theme` — Choose between solid and outlined buttons

### `calendar`
A visual calendar for selecting a date. Stores value as a `date`.

* `min` — The minimum selectable date
* `max` — The maximum selectable date
* `formatting` — A string that determines how the date will be displayed

### `checkboxes`
An array of options presented as checkboxes, where multiple can be selected. Stores value as an `array`.

* `choices` — Key-value pairs of options
* `allow_other` — Whether new values can be manually added by the user
* `draggable` — Whether options can be reordered
* `wrap` — Whether to display options in a grid or list
* `formatting` — Displays as CSV or formatted options on listing page
* `single` — TK

### `checkboxes-relational`
The same as `checkboxes`, but values come from a relational dataset. Stores relationally as a `o2m`.

* `grid` — The number of columns for checkboxes
* `template` — A micromustache template string of relational fields to display on the edit page
* `listing_template` — A micromustache template string of relational fields to display on the listing page

### `code`
A simple code editor with syntax coloring based on language.

* `language` — The code language of the interface (eg: `application/json`)
* `template` — Example/boilerplate code that can be added when clicking a button
* `lineNumber` — Toggle showing line numbers

### `collections`
A dropdown for selecting a collection from the current project. Stores value as a `string`.

* `placeholder` — Placeholder text for the dropdown
* `include_system` — Toggles including system collections

### `color`
Allows selecting a color from a palette or full color chooser, and saving it in different formats. Stores value as a `string`.

* `format` — How to save the value: Hex, RGB, or HSL
* `palette` — Default color options available to choose from
* `paletteOnly` — Restricts choices to the palette options

### `color-palette`
Allows selecting from the Material Design palette (eg: `light-blue-600`). Stores value as a `string`.

### `date`
A standard browser date input. Stores value as a `date`.

* `min` — The minimum selectable date
* `max` — The maximum selectable date
* `localized` — Toggles showing dates in the user's local
* `showRelative` — Toggles showing dates relatively (eg: `12 minutes ago`)
* `iconLeft` — An icon to show on the left side of the input
* `iconRight` — An icon to show on the right side of the input

### `datetime`
A masked input for entering a datetime. Stores as a `datetime`, `datetime_updated` or `datetime_created`.

* `min` — The minimum selectable date
* `max` — The maximum selectable date
* `localized` — Toggles showing dates in the user's local
* `showRelative` — Toggles showing dates relatively (eg: `12 minutes ago`)
* `iconLeft` — An icon to show on the left side of the input
* `iconRight` — An icon to show on the right side of the input
* `defaultToCurrentDatetime` — Toggles defaulting to NOW
* `format` — Selects a format for displaying the value

### `datetime-created`
A readonly interface for saving/showing when an item was created. Stores value as a `datetime_created`.

* `showRelative` — Toggles showing dates relatively (eg: `12 minutes ago`)

### `datetime-updated`
A readonly interface for saving/showing when an item was last modified. Stores value as a `datetime_updated`.

* `showRelative` — Toggles showing dates relatively (eg: `12 minutes ago`)

### `divider`
Adds a title and/or horizontal rule that can help organize forms in the app. Does not store data (`alias`).

* `style` — The size of the divider (Small, Medium, or Large)
* `title` — An optional title to add
* `description` — Additional optional text shown smaller below the title
* `hr` — Toggles the horizontal rule in the divider
* `margin` — Toggles an increased margin above the divider (for more delineated grouping)

### `dropdown`
A standard dropdown of options allowing a single selection. Stores value as a `string`.

* `choices` — Key-value pairs of options
* `placeholder` — Placeholder text for the dropdown
* `formatting` — Displays as CSV or formatted options on listing page
* `icon` — An icon to show on the left side of the input

### `file`
Allows uploading or selecting a single file. Stores value as a `file` (FK to `directus_files`).

* `crop` — Toggles showing a cropped or full thumbnail
* `viewType` — Layout for the display (eg: cards)
* `viewOptions` — JSON for configuring the layout settings
* `viewQuery` — JSON for configuring the layout settings
* `filters` — What filters to use
* `accept` — A list of MIME types that can be uploaded/selected

### `files`
Allows uploading or selecting multiple files. Stores relationally as a `o2m` (part of `m2m` to `directus_files`).

* `fields` — A CSV of fields to display
* `template` — A micromustache template string of relational fields to display on the edit page
* `allow_create` — Toggles a button for creating/uploading new files
* `allow_select` — Toggles a button for selecting existing files
* `accept` — A list of MIME types that can be uploaded/selected

### `file-preview`
A full-sized file preview that allows editing images (eg: cropping/resizing). Stores value as a `string`.

* `edit` — Toggles the images editor

::: warning System Only
For now, this interface can/should only be used within system collections.
:::

### `file-size`
Renders a filesize (in bytes) as human-readable. Stores value as a `integer`.

* `placeholder` — Placeholder text for the input
* `format` — Whether to format the display
* `formatInput` — Whether to format the input
* `decimal` — Whether to include decimals in the value

### `hashed`
Stores an encrypted string. Stores value as a `hash`.

* `hide` — Whether to show text or "bullets" as it's typed
* `placeholder` — Placeholder text for the input
* `showHash` — Toggles making the hash visible or not
* `hashingType` — The algorithm to use (eg: `bcrypt`)

### `icon`
Allows choosing a Material Design icon. Stores value as a `string`.

### `json`
Similar to the code interface, this is dedicated for JSON and includes error reporting. Stores value as `json`.

* `template` — Example/boilerplate code that can be added when clicking a button

### `key-value`
Allows creating key-value pairs. Stores value as `json`.

* `keyInterface` — The interface to use for inputting the key
* `keyType` — The "datatype" to use for the key (eg: `string`)
* `keyOptions` — Any interface options for the selected `keyInterface`
* `valueInterface` — The interface to use for inputting the value
* `valueType` — The "datatype" to use for the value (eg: `string`)
* `valueOptions` — Any interface options for the selected `valueInterface`

### `language`
Allows selecting a language key. Stores value as a `string` or `lang`.

* `limit` — Limits choices to the available languages

### `many-to-many`
Allows managing items related through a junction table. Stores relationally as a `o2m` (part of `m2m` to another collection).

* `fields` — a CSV of fields to display
* `template` — A micromustache template string of relational fields to display
* `allow_create` — Toggles a button for creating/uploading new files
* `allow_select` — Toggles a button for selecting existing files

### `many-to-one`
Allows creating/selecting a single relational item. Stores relationally as a `m2o`.

* `template` — A micromustache template string of relational fields to display
* `visible_fields` — A CSV of visible fields
* `placeholder` — Placeholder text for the input
* `threshold` — The number of fetched items when the interface shows a modal instead of a dropdown
* `icon` — An icon to show on the left side of the input

### `map`
A map for visually selecting a lat/long. Stores relationally as `json`.

* `height` — The height of the map in pixels
* `mapLat` — Initial latitude for map
* `mapLng` — Initial longitude for map
* `defaultZoom` — Initial zoom for map
* `maxZoom` — The maximum zoom allowed in the map
* `address_to_code` — Adds an input for entering (and geocoding) an address
* `theme` — Choose between a color and grayscale map

### `markdown`

* `tabbed` — Switches between a tabbed and side-by-side editor
* `rows` — How many rows of text to display (height)
* `placeholder` — Placeholder text for the input

### `multiselect`
A standard browser multiselect that allows choosing multiple options. This is done using the SHIFT/META keys when selecting. Stores value as an `array`.

* `choices` — Key-value pairs of options
* `placeholder` — Placeholder text for the input
* `size` — How many rows of options to display (height)
* `wrapWithDelimiter` — Wraps the value with extra delimiters (eg: `,5,10,15,` vs `5,10,15`) for more exact searching. In that example, searching for `5` will give false positives for `15`, but searching for `,5,` will not.
* `formatting` — Switches between showing the display text or value

### `numeric`
A standard browser numeric input. Stores value as an `integer` or `decimal`.

* `placeholder` — Placeholder text for the input
* `localized` — Toggles showing dates in the user's local
* `iconLeft` — An icon to show on the left side of the input
* `iconRight` — An icon to show on the right side of the input
* `monospace` — Toggles the interface using a monospace font

### `one-to-many`
Allows creating/selecting items that are stored relationally in another collection. Stores relationally as a `o2m`.

* `fields` — A CSV of fields to display
* `template` — A micromustache template string of relational fields to display on the edit page
* `sort_field` — The field to use for manual sorting (drag and drop reordering)
* `allow_create` — Toggles a button for creating/uploading new files
* `allow_select` — Toggles a button for selecting existing files
* `delete_mode` — Switches between nullifying the relationship or deleting the related item

### `password`
Stores an encrypted string. Stores value as a `hash` or `string`.

* `hide` — Whether to show text or "bullets" as it's typed
* `placeholder` — Placeholder text for the input
* `showHash` — Toggles making the hash visible or not
* `hashingType` — The algorithm to use (eg: `bcrypt`)

### `preview`
Displays a button that opens a custom external URL when clicked. Used for opening a preview of website addresses. Does not store data (`alias`).

* `url_template` — A micromustache template string to generate the URL (eg: `https://example.com/articles/{{id}}`)

### `primary-key`
An input for editing integer or string keys, that is not editable after creation. Stores value as an `integer` or `string`.

* `monospace` — Toggles the interface using a monospace font

### `radio-buttons`
An array of options presented as radio buttons, where a single option can be selected. Stores value as a `string`.

* `choices` — Key-value pairs of options
* `format` — Selects a format for displaying the value

### `rating`
A clickable row of stars for viewing/saving ratings. Stores value as a `integer` or `decimal`.

* `active_color` — The Material Design color used for selected stars
* `max_stars` — The maximum number of stars in the rating
* `display` — How to display the value on the listing page (numbers or stars)

### `repeater`
A highly customizable interface for creating repeatable "items" using other interfaces. Stores value as `json`.

* `template` — A micromustache template string of fields (see below) to display in the row header
* `fields` — The fields to include within each repeated item
* `placeholder` — The placeholder text shown in the row header
* `createItemText` — The text displayed on the "Create Item" button
* `limit` — The maximum number of items that can be created
* `structure` — Whether to store the values as an Array or Object
* `structure_key` — The field "key" to be used as the object key (see above)

### `slider`
A standard slider bar for setting a number, with or without steps. Stores value as a `integer`.

* `min` — The minimum selectable value
* `max` — The maximum selectable value
* `step` — The step increment or values
* `unit` — A text label for showing units

### `slug`
Maps to another text field, converting the string to a URL safe version. Stores value as a `string`.

* `placeholder` — The placeholder text shown in the input
* `forceLowercase` — Toggles forcing the value to be lowercase
* `mirroredField` — The name of the field to mirror

### `sort`
Used in layouts and other interfaces to set/save manual sort values. Stores value as a `sort` (sequential integers).

### `status`
Defines whether an item is published, soft-deleted, or some other custom workflow state. Stores value as a `status` (a string).

* `simpleBadge` — Switches between a simple circle of color, and a full-colored text badge
* `status_mapping` — A repeater for setting up the workflow/status options

[Learn more about Status Mapping](/guides/status.html)

### `tags`

* `alphabetize` — Toggles saving values in alphabetical order
* `lowercase` — Toggles forcing tags to be in lowercase
* `wrap` — Toggles tags showing in a grid or list
* `format` — Whether to show values as text CSV or stylized tags
* `sanitize` — Removes special characters
* `iconLeft` — An icon to show on the left side of the input
* `iconRight` — An icon to show on the right side of the input
* `validation` — A RegEx for individual tags
* `validationMessage` — A message to display if the RegEx validation fails

### `text-input`
A standard single-line text input. Stores value as a `string` or `lang`.

* `placeholder` — The placeholder text shown in the input
* `trim` — Toggles trimming whitespace from the beginning/end of the value
* `showCharacterCount` — Shows the remaining characters left (based on length)
* `iconLeft` — An icon to show on the left side of the input
* `iconRight` — An icon to show on the right side of the input
* `formatValue` — Uses the title formatter for the displayed value
* `monospace` — Toggles the interface using a monospace font

### `textarea`
A standard multi-line textarea. Stores value as a `string`.

* `rows` — How many rows of text to display (height)
* `placeholder` — Placeholder text for the input
* `serif` — Switches between a Serif and Sans-Serif font for the input

### `time`
A standard time input. Stores value as a `time`.

* `display24HourClock` — Switches between a 12 and 24 time

### `toggle`
Shows a switch or checkbox that can be on or off. Stores value as a `boolean`.

* `labelOn` — A text label shown when the value is true
* `labelOff` — A text label shown when the value is false
* `checkbox` — Switches presentation between a Switch and Checkbox

### `toggle-icon`
Toggles between two icons depending on if the value is true or false. Stores value as a `boolean`.

* `textInactive` — A text label shown when the value is false
* `iconInactive` — An icon for when the value it false
* `colorInactive` — A color for when the value is false
* `textActive` — A text label shown when the value is true
* `iconActive` — An icon for when the value it true
* `colorActive` — A color for when the value is true

### `translation`
Stores relationally as a `translation` (`o2m`).

* `languageField` — The name of the field in the related collection that stores the language key
* `languages` — Key-value pairs of allowed languages and language codes
* `template` — A micromustache template string of relational fields to display

### `user`
A dropdown for selecting a Directus User. Stores value as a `user`, `integer`, `user_created`, or `user_updated`.

* `template` — A micromustache template string of relational fields to display
* `placeholder` — The placeholder text shown in the input

### `user-created`
CONVERTED TO `owner`. Stores value as a `user_created`.

* `template` — A micromustache template string of relational fields to display
* `display` — A dropdown for selecting whether to show an avatar, name, or both

### `user-roles`
A dropdown to select from a dynamic list of Directus Roles. Stores relationally as a `m2o`.

* `showPublic` — Include the public role

### `user-updated`
A readonly interface for saving/showing which user last modified the item. Stores value as `user_updated`.

* `template` — A micromustache template string of relational fields to display
* `display` — A dropdown for selecting whether to show an avatar, name, or both

### `wysiwyg`
Includes buttons for formatting rich-text and saving it as HTML. Stores value as a `string`.

* `toolbar` — For choosing which standard (TinyMCE)toolbar buttons are available
* `custom_formats` — Allows you to add custom formatting buttons/styles to the editor

## System Interfaces

This subset of Core Interfaces were built specifically for use within Directus Settings. Please be aware that these interfaces are ad hoc and might not work well for project content.

* `2fa-secret`
* `activity-icon`
* `interface-options`
* `interfaces`
