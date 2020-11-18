# Installing from source

::: danger Advanced
You most likely don't need this. Please don't install Directus from source unless you're absolutely sure of what you're doing.
:::

When you have a complex infrastructure that requires special CI and CD integrations that will handle building dependencies etc, you might want to build from source. 

[[toc]]

::: tip CI/CD
If you're thinking about using this to auto-deploy new versions of Directus, you can also consider using a build script to remove the `vendor` folder and re-install the dependencies using composer. That will net you the same end-result will still being able to rely on the GitHub releases.
:::

## Step 1: Clone the app and API repos separately

The `directus/directus` repo contains a pre-built bundle for both at all times. There is no "source bundle" available. You have to clone the individual repos for the app and API; `directus/app` and `directus/api` respectively.

Make sure to be on the latest tagged release. You can use this snippet to get to the latest tag (h/t to [Julien Renaux](https://julienrenaux.fr/2013/10/04/how-to-automatically-checkout-the-latest-tag-of-a-git-repository/)):

```
$ git fetch --tags

$ latestTag=$(git describe --tags `git rev-list --tags --max-count=1`)

$ git checkout $latestTag
```

## Step 2: Install the dependencies

#### App

The app uses Vue and manages its dependencies through NPM. Install the npm dependencies using npm or yarn. 

#### API

The API uses Composer for its package management. Install the dependencies using Composer.

## Step 3: Build the app for production

Running `npm run build` will build the app production ready to the `dist` folder.

## Step 4: Move the app inside the API.

The app expects to be served from the APIs `/public/admin` folder. Therefore, you have to move the contents of the app's `dist` folder into the APIs `/public/admin` folder.

## (Optional) Step 5: Remove unnecessary files

Depending on your needs, you might not want / need some of the files in the API repo. The folders that are required to run Directus are: `bin`, `config`, `logs`, `migrations`, `public`, `src`, `vendor`.
