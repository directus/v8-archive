/**
 * @see https://docs.directus.io/api/reference.html#folder-object
 */
export interface IFolder {
  id: number;
  name: string;
  parent_folder?: string;
}
