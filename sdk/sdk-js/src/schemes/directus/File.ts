/**
 * @see https://docs.directus.io/api/reference.html#file-object
 */
export interface IFile {
  id: number;
  storage: string;
  filename: string;
  title: string;
  type: string;
  uploaded_by: number;
  uploaded_on: Date;
  charset: string;
  filesize: number;
  width: number;
  height: number;
  duration: number;
  embed: string;
  folder: string;
  description: string;
  location: string;
  tags: string | string[];
  metadata: object;
  data: object;
}
