/**
 * @see https://docs.directus.io/api/reference.html#activity-object
 */
export interface IActivity {
  action: string;
  action_by: number;
  action_on: Date;
  collection: string;
  comment?: string;
  comment_deleted_on?: Date;
  edited_on?: string;
  id: number;
  ip: string;
  item: string;
  user_agent: string;
}
