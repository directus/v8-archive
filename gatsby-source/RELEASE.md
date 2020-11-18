# Release

This guide will help you to release and publish a new version for this package.

Before making a new release, be sure that the project passes linting and the tests, otherwise it won't be published to the NPM package registry.

```sh
yarn lint
yarn test
```

First, we will bump the package version number, commit the change and finally tag the commit with our new version. This is done with the help of the `npm version` command (see `npm help version`).

```sh
yarn release [<newversion> | major | minor | patch]
```

The release commit and tag should have been pushed to the Github repository. If the `postrelease` step failed, run the following command:

```sh
git push --follow-tags
```

Github will take care of building the project, running all the tests, and finally publishing the package to the NPM registry.

The next step is to edit the auto generated release draft on Github to create a new release attached to the tag that we previously pushed.

The release draft may contain some useless changes, so remove any unnecessary logs from it. Once the release draft is ready, it can be published.
