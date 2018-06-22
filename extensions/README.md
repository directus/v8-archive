# Directus Core Extensions

These are the un-bundled / non-minified (core) extensions, like interfaces and listings.

## Installation

These extensions are included in the production copy of the API by default. No further installation necessary. 

To work on these, you have to install the API locally according to [the local installation instructions](https://github.com/directus/api/wiki/Installing-the-API-locally).

## Usage

To build the extensions production ready, run `npm run build`. This will bundle all the .vue files and move them into the public/extensions/core folder.

By running `npm run dev`, a watcher is fired up which allows you to work on the extensions with hot module reloading enabled.
