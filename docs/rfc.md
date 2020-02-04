# Schema management

## How to allow schemas to be migrated from one stage to the next?

-   Keep a history of all changes made to the schema (structure only)
-   Use CQRS / Event Sourcing (?)
    -   Allows to replay the changes on another database
    -   Needs to be easily exported to a serializable format
    -   Look into http://getprooph.org/ (?)
-   What if the user changes the database schema outside directus?
    -   Needs to validate database schema structure before replaying stored events

## How to make Directus extensible?

-   Event based? Hooks?
-   What about concurrency?
