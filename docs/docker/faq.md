# FAQ

## How can I update my images?

We push updates to the same tags using `semver`, so depending on what tags you use, by pulling a new image you'll receive different updates. For more information on our versioning, please check [the tags section](/docker/docker.md#tags) of our documentation.

### Pull to receive only `minor`, `patch` and `hotfix` updates

Use `directus/directus:vX-apache`

### Pull to receive only `patch` and `hotfix` updates

Use `directus/directus:vX.Y-apache`

### Pull to receive only `hotfix` updates

Use `directus/directus:vX.Y.Z-apache`

::: tip 
We do recommend using tags down to the `patch` level (to receive only hotfixes), because this makes it unlikely to break your installation whenever breaking changes are introduced to directus. Pinning to `patch` will still allow you to receive hotfixes and security updates.
:::

## I want to use files for configs, what should I do?

Please refer to [disabling environment variables](/docker/overview.md#disabling-environment-variables) for more information.
