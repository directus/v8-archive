import { IAPIMetaList, IAPIResponse } from "../APIResponse";
import { IFile } from "../directus/File";

/**
 * @see https://docs.directus.io/api/reference.html#file-object
 */
export interface IFileResponse<T extends IFile = IFile> extends IAPIResponse<T> {}

/**
 * @see https://docs.directus.io/api/reference.html#file-object
 */
export interface IFilesResponse<T extends IFile[] = IFile[]> extends IAPIResponse<T[], IAPIMetaList> {}
