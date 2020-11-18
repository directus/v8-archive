# deploy-script
The script that we use to automate the release of new versions of Directus to directus/directus:master

**DO NOT USE**

Unless you have admin privileges and you're in charge of creating a new release, you won't have to use this. (It also won't work).

## Installation

Clone this repo

Add write permissions to the `deploy.sh` file

```
chmod +x ./deploy.sh
```

## Usage

1. Update the version number in package.json (app) and src/core/Directus/Application/Application.php (api)
2. Update the `master` branch on directus/app and directus/api
3. Tag the release on the `master` branch of directus/app and directus/api
4. Run the script `./deploy.sh`
