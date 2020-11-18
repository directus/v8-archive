# Basic Multi-Tenancy

## Prerequisites

- Make sure docker and docker-compose is available
- Make sure nothing is running on port 8443
  - If necessary, adjust the exposed ports on docker-compose.yml file

## Make sure you have the latest images

```
docker-compose pull
```

## Run the stack

```
docker-compose up -d
```

## Create the projects

Create tenants by accessing `https://localhost:8443/admin/#/install`

> NOTE: After the first tenant is created, you'll need to take note of the super admin token. If you can't remember it, it's located in `data/config/__api.json` file on host, or `/var/directus/config/__api.json` within the container.

This example executes two different databases `project1` and `project2`.

**Please make sure you understand how docker networking works before raising database connectivity related issues.**

While you can still connect to an external database, in this example the database `Host` field should be either `project1` or `project2` if you want to use the example's databases ([see composer docs](https://docs.docker.com/compose/networking/)).

- `Host` is either `project1` or `project2`
- `Port` is `3306`
- `User` is `directus`
- `Database User Password` is `directus`
- `Database Name` is `directus`

To change these values, check the `docker-compose.yml` file.

## Accessing

- [Create Tenant](https://localhost:8443/admin/#/install)
- [Directus](https://localhost:8443/)
