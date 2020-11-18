<p align="center">
  <a href="https://directus.io" target="_blank" rel="noopener noreferrer">
    <img src="https://user-images.githubusercontent.com/522079/43096167-3a1b1118-8e86-11e8-9fb2-7b4e3b1368bc.png" width="140" alt="Directus Logo"/>
  </a>
</p>

<p>&nbsp;</p>
<h1 align="center">
  Directus 8
</h1>

<h3 align="center">
  <a href="https://directus.io">Website</a> •
  <a href="https://docs.directus.io">Docs</a> •
  <a href="https://docs.directus.io/api/reference.html">API Reference</a> •
  <a href="https://docs.directus.io/guides/user-guide.html">User Guide</a> •
  <a href="https://directus.app">Demo</a> •
  <a href="https://docs.directus.io/getting-started/supporting-directus.html">Contribute</a>
</h3>

<p>&nbsp;</p>

# Overview

Directus provides several container images that will help we get started. All our container images can be found in [docker hub](https://hub.docker.com/r/directus/).

# Reference

- [Docker issues](https://github.com/directus/docker/issues)
- [Docker quick install](https://docs.directus.io/installation/docker.html)
- [Docker documentation](https://docs.directus.io/docker/overview.html)
- [Environment variables](https://docs.directus.io/docker/environment.html)
- [Slack channel #docker](https://directus.chat/)

# Environment variables

Our full list of environment variables can be found on our [documentation page](https://docs.directus.io/docker/environment.html). Please make sure to take a look over our overview page as there are some required variables to get run the images.

# Versioning

We publish major, minor and patch versions to docker hub, as well as hotfixes and security updates. This means that you can choose between:

- Patches `v8.2.0`
  > Updates to this tag will receive hotfixes and security updates
- Minor `v8.2`
  > Updates to this tag will receive all patches, hotfixes and security updates
- Major `v8`
  > Updates to this tag will receive all minor updates, patches, hotfixes and security updates

# Building

In most cases you'll not need to build anything in this repository because we already distribute built images through docker hub. But if you want to, you'll be able to easily build them with our build script.

## Requirements

- [Docker](https://docs.docker.com/install/)
- bash

---

## Executing the build script

We can build images using the command `build` located in bin folder.

> Note: If you're getting "-A: invalid option" issues, try updating your bash console. OSX for example ships with older bash versions. These scripts will only work on bash 4 or newer.

```
# Clone the repository
git clone https://github.com/directus/docker.git

# Open the repository directory
cd docker

# Invoke build script
./bin/build --help
```
