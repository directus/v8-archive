# extension-toolkit
[WIP] Toolkit to help you build your own custom extensions!

## Installation

```
npm install -g @directus/extension-toolkit
```

## Usage

To create a new extension, run `directus-extensions create [type] [name]`:

```
directus-extensions create interface my-first-interface
```

This will create a folder in the current directory with all the files you need to create your own extension.

## Building the extensions

An extension needs to be transpiled (from Vue to JS) in order for the application to use it. The previous step installed the tooling necessary to do this.

### Commands
These should be run from inside the folder created in the previous step.

```
npm run build
```
This transpiles files from the `./src` folder and outputs the files into the `./dist` folder


```
npm run build -- --input ./path/to/src/folder --output ./path/to/output/folder
```
This works the same as the previous `build` command, but allows you to specify an input and output folder, instead of using the default values.


```
npm run dev
```
This transpiles files from the `./src` folder and outputs the files into the `./dist` folder. It will watch for changes in all files inside `./src` and re-transpile whenever new changes are detected.


```
npm run dev -- --input ./path/to/src/folder --output ./path/to/output/folder
```
This works the same as the previous `dev` command, but allows you to specify an input and output folder, instead of using the default values.

## Developing an extension

When working on an extension, it is common to have an instance of Directus running to test any changes made to the extension. However, the default output of `./dist` that `npm run build` and `npm run dev` use won't allow Directus to see the transpiled extension output.

We recommend something like the following setup:

```
# Your directus installation
directus
├── ...
└── public
    └── extensions
        ├── core
        └── custom
            ├── ...
            └── interfaces # Or the type of extension you're working on
                └── my-first-interface

# Your development folder
development
└── my-first-interface
    ├── package.json
    ├── src
    └── readme.md

```
where `directus` is the folder where your running Directus instance is, and `development` is where you store your version-controlled extension source code.

For the above setup, run the following command from inside `/path/to/development/my-first-interface` to build the extension into Directus
```
npm run build -- --input ./src --output /path/to/directus/public/extensions/custom/interfaces/my-first-interface
```
If you're actively developing, you can use the `npm run dev` command with the same input/output options.

### ⚠️Warning
This project does *not* include livereload or Hot Module Reloading. You will need to manually refresh the Directus webpage to see your changes. Additionally, make sure you [disable cach](https://www.technipages.com/google-chrome-how-to-completely-disable-cache) to ensure your changes are loaded.
