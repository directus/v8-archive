/**
 * @see https://docs.directus.io/api/reference.html#role-object
 */
export interface IRole {
  id: number;
  name: string;
  description: string;
  ip_whitelist: string;
  nav_blacklist: string;
  external_id: string;
}
