/**
 * @see https://docs.directus.io/api/reference.html#activity-seen-object
 */
export interface IActivitySeen {
  id: number;
  activity: number;
  user: number;
  seen_on: Date;
  archived: boolean;
}
