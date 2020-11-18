# Directus Style Guide

> A lot of thought has gone into the Directus brand, user-experience (UX), and user-interface (UI). Therefore it is important to follow this guide precisely so that consistency is maintained across the App and all extensions.

## CSS Variables

To keep things standardized across many extensions, we have a global set of CSS variables that are available within the App. Whenever possible, these variables should be used. All the variables in use by the Directus App can be found in [the variables.scss file](https://github.com/directus/app/blob/b43f9e56e3d1e3a47c7a4d201bb8a542c0a6cf44/src/design/_variables.scss).

## Breakpoints

Directus uses 4 main breakpoints:

* Small: 0 - 800px
* Medium: 800px - 1000px
* Large: 1000px - 1200px
* Extra Large: 1200px - Infinity

## Typography

All type is rendered in the Roboto typeface with four weights: light (300), regular (400), medium (500), and bold (700).

## Iconography

Directus uses the [Material Design Icon Set](https://material.io/icons/), and only this set should be used unless otherwise warranted. Every attempt should be made for icons to be clear and optically aligned using Google's guide. Proper sizing for icons includes: 18px, 24px, 36px, 48px, etc.

Icons used within bordered interfaces (eg: Text Input) should typically be 8px from the edges, or 20px for more padded bordered interfaces (eg: WYSIWYG)

When an icon is to be used within a box/container, proper optical alignment is crucial. Some icons are "visually weighted" to one side (or have odd pixel dimensions at certain sizes) and should be adjusted from true center by 1-2 pixels to compensate.

## Colors

Directus uses the [Material Design Color Palette](https://www.materialui.co/colors). The entire Material design palette is available as global CSS variables, as well as many aliases that are used in our theming.

### Example Color Aliases

```css
--brand: var(--blue-grey-900);
--accent: var(--blue-500);
--action: var(--blue-grey-800);
--success: var(--green);
--warning: var(--orange);
--danger: var(--red);
```

### Interface Sizing

Field interfaces are either half (300px) or full width (632px). These widhts are especially important when creating interfaces, as it makes sure that all interfaces align nicely within the edit forms.

Interfaces should use an intuitive default height based on the specific input. Whenever possible, height should be made into an Option so that the user may configure this to the content's needs.

::: warning
Single-line interfaces (inputs, dropdowns, buttons, etc) should always try to be exactly 40px in total height (including borders) unless otherwise warranted.
:::

### Text Overflow

When working with dynamic text elements, you'll often need to ensure longer text is cut off with an ellipsis at the end. To make this a little bit easier, you can add the `no-wrap` class. This stops text from wrapping and adds ellipsis where the text gets cut of. This is especially useful in the readonly component of interfaces, seeing those are being used on listing pages where space is often tight.

### Spacing

Directus uses 4px unit spacing for size, padding, and margin: 4px, 8px, 12px, 16px, 20px, etc.

## Interface States

* Default
* Readonly & Disabled
* Hover
* Focus
* Invalid
