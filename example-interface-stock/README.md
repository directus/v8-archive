# Example Interface (Stock)

This is an example interface for Directus 7.

You can use this repo as a starting point for creating your own custom interface

## Requirements

The interfaces uses [Parcel](https://parceljs.org/) to build the `.vue` files to (Directus compatible) `.js` files. 

In order to run Parcel, you need [Node.js](https://nodejs.org) (preferably LTS) installed.

## Installation

Clone this repo and run `npm install` to install the npm dependencies.

## Usage

### Building the extension

To build the `.vue` files to make them ready for use in Directus, you can run `npm run build`. This will generate everything in the dist folder.

### Installing the extension into Directus

Remember that that Directus extensions are actually stored in the API codebase. Therefore, in the Directus API:

1. Navigate to the directory `/public/extensions/custom/interfaces/`
2. Create a folder called `my-interface` (or any other name)
3. Put the contents of the `dist` folder in that newly created `my-interface` folder
