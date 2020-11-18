# Accountability

> Directus stores detailed records of every change made to data through the App and API. This gives a comprehensive overview of who did what, and when.

## System Fields

There are four system Interfaces (and their respective Field Types) that are important to accountability:

* `created_by` — Automatically saves the Directus User that created the item
    * This interface is required for [`mine` and `role` permissions](/guides/permissions.html#collection-level)
* `created_on` — Automatically saves the UTC datetime when the item was created
* `modified_by` — Automatically saves the Directus User that last modified the item
* `modified_on` — Automatically saves the UTC datetime when the item was last modified

## Activity

This is a log of all platform activity. You can see a listing of all _your_ activity within the App by opening the Activity page from the user menu. You can also see the full history for a specific item by opening the Info Sidebar on its Item Detail page.

There are several different types of activity that is tracked:

* `create` — An item is created
* `update` — An item is updated
* `delete` — An item is hard deleted
* `revert` — An item is reverted to a different revision
* `soft-deleted` — An item's status is changed to a soft deleted option
* `authenticate` — A Directus user signs in
* `upload` — A file is uploaded to the File Library
* `comment` — A comment was left on an item

::: tip Skip Activity Log
If you need to interact with data without saving to the activity log there is an API parameter to [_skip_ the activity log](/api/reference.html#skip-activity-logging). This bypasses accountability and should be used *judiciously*.
:::

## Revisions

In addition to the activity log, every time an item is created or updated Directus creates a revision record. Each revision is stored within `directus_revisions` and contains both a delta of changes and a full snapshot.

::: tip API Access
Item revision can be fetched from the API using the [revisions endpoint](/api/reference.html#get-item-revision). This is a 0-based index that allows for fetching revisions based on creation point or current state.
:::

## Revert

Revisions are stored within (and fetched from) the system schema. However to shift the actual database item to a different point in its revision history you would use "revert". While primarily used to roll-backwards in time, this can also be used to move forward. Whenever an item is reverted, _new_ activity and revision records are created with a `revert` action instead of `update`.

You can use the API to revert an item, or you can revert through the App:

1. Go to the detail page of the item you want to revert
2. Open the Info Sidebar, expanding revisions it to see the diff
3. Click on the "Revert" button to preview the revision
4. Confirm the revert in the preview modal