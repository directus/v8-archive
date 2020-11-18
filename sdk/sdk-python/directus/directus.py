# -*- coding: utf-8 -*-

from dataclasses import asdict
from typing import List, Optional, Tuple, Union

from .exceptions import DirectusException
from .utils import ApiClient
from .typing import (
    RequestMeta,
    RequestFields,
    ResponseMeta,
    Collection,
    Item,
    Revision,
    File,
)


class DirectusClient:
    """
    DirectusClient provide a way to interact with Directus API on a defined server.
    It eases the use of [Directus API](https://docs.directus.io/api/reference.html) by providing
    simple methods to access to the resources.

    Attributes
    ----------
    url: str
        The url of the Directus server you want to connect to

    email: str
        The email account you want to use to connect to the Directus server

    password: str
        The associated password corresponding to the email account

    project: str
        The name of the project you want to access on the specified server
    """

    def __init__(
        self,
        url: Optional[str] = None,
        email: Optional[str] = None,
        password: Optional[str] = None,
        project: Optional[str] = None,
    ):
        if not url:
            raise DirectusException("You must provide a server url")

        if not project:
            raise DirectusException("You must provide a project")

        if not email:
            raise DirectusException("You must provide an email")

        if not password:
            raise DirectusException("You must provide a password")

        self.ApiClient = ApiClient(
            url=url, email=email, password=password, project=project
        )

    """

    Collections
    https://docs.directus.io/api/collections.html

    """

    def get_collections_list(
        self, offset: int = 0, single: bool = False, meta: RequestMeta = []
    ) -> Tuple[List[Collection], ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/collections.html#list-collections

        Returns
        -------
            (List of Collection, Metadata)
        """
        path = "collections"

        params = {"offset": offset, "single": int(single)}
        response_data, response_meta = self.ApiClient.do_get(
            path=path, params=params, meta=meta
        )

        return list(response_data), response_meta

    def get_collection(
        self, collection: str, meta: RequestMeta = []
    ) -> Tuple[Collection, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/collections.html#retrieve-a-collection

        Returns
        -------
            (Collection, Metadata)
        """
        path = "/".join(["collections", collection])

        return self.ApiClient.do_get(path=path, meta=meta)

    def create_collection(
        self, collection: Collection, meta: RequestMeta = []
    ) -> Tuple[Collection, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/collections.html#create-a-collection

        Returns
        -------
            (Collection, Metadata)
        """
        path = "collections"

        return self.ApiClient.do_post(path=path, data=asdict(collection), meta=meta)

    def update_collection(
        self, collection: str, data: dict, meta: RequestMeta = []
    ) -> Tuple[Collection, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/collections.html#update-a-collection

        Returns
        -------
            (Collection, Metadata)
        """
        path = "collections"

        response_data, response_meta = self.ApiClient.do_patch(
            path=path, id=collection, data=data, meta=meta
        )

        return response_data, response_meta

    def delete_collection(self, collection: str) -> bool:
        """
        Find out more: https://docs.directus.io/api/collections.html#delete-a-collection

        Returns
        -------
            bool (True if deleted, False if not)
        """
        path = "collections"

        is_deleted = self.ApiClient.do_delete(path=path, id=collection)

        return is_deleted

    """

    Items
    https://docs.directus.io/api/items.html

    """

    def get_items_list(
        self,
        collection: str,
        fields: RequestFields = ["*"],
        page: Optional[int] = None,
        limit: int = 100,
        offset: int = 0,
        sort: List[str] = ["id"],
        single: bool = False,
        filter: dict = {},
        status: Optional[str] = None,
        q: Optional[str] = None,
        meta: RequestMeta = [],
    ) -> Tuple[List[Item], ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#list-the-items

        If page is set, offset is not taken into account

        If single, only return first corresponding result from list

        Returns
        -------
            (List of Item, Metadata)
        """
        path = "/".join(["items", collection])

        params = {
            "fields": ",".join(fields),
            "limit": limit,
            "offset": offset,
            "sort": ",".join(sort),
            "single": single,
            "filter": filter,
            "status": status,
            "q": q,
        }

        if page:
            params["page"] = page
            del params["offset"]

        response_data, response_meta = self.ApiClient.do_get(
            path, params=params, meta=meta
        )

        return list(response_data), response_meta

    def get_all_items_list(
        self,
        collection: str,
        fields: RequestFields = ["*"],
        sort: List[str] = ["id"],
        filter: dict = {},
        status: Optional[str] = None,
        q: Optional[str] = None,
        meta: RequestMeta = [],
        page: int = 1,
    ) -> Tuple[List[Item], ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#list-the-items

        Returns
        -------
            (List of Item, Metadata)
        """
        path = "/".join(["items", collection])

        if "page" not in meta:
            meta.append("page")

        response_data, response_meta = self.get_items_list(
            collection=collection,
            fields=fields,
            sort=sort,
            filter=filter,
            page=page,
            status=status,
            q=q,
            meta=meta,
        )

        if int(response_meta["page"]) < int(response_meta["page_count"]):
            next_page = int(response_meta["page"]) + 1
            recursive_data, _ = self.get_all_items_list(
                collection=collection,
                fields=fields,
                page=next_page,
                sort=sort,
                filter=filter,
                status=status,
                q=q,
                meta=meta,
            )

            response_data += recursive_data

        return response_data, response_meta

    def get_item(
        self,
        collection: str,
        id: int,
        fields: RequestFields = ["*"],
        meta: RequestMeta = [],
    ) -> Tuple[Item, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#retrieve-an-item

        Returns
        -------
            (Item, Metadata)
        """
        path = "/".join(["items", collection, str(id)])

        params = {"fields": ",".join(fields)}

        return self.ApiClient.do_get(path=path, params=params, meta=meta)

    def create_item(
        self, collection: str, item: Item, meta: RequestMeta = []
    ) -> Tuple[Item, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#create-an-item

        Returns
        -------
            (Item, Metadata)
        """
        path = "/".join(["items", collection])

        return self.ApiClient.do_post(path=path, data=item, meta=meta)

    def update_item(
        self,
        collection: str,
        id: int,
        data: dict,
        fields: RequestFields = ["*"],
        meta: RequestMeta = [],
    ) -> Tuple[Item, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#update-an-item

        Returns
        -------
            (Item, Metadata)
        """
        path = "/".join(["items", collection])

        return self.ApiClient.do_patch(path=path, id=id, data=data, meta=meta)

    def delete_item(self, collection: str, id: int) -> bool:
        """
        Find out more: https://docs.directus.io/api/items.html#delete-an-item

        Returns
        -------
            bool (True if deleted, False if not)
        """
        path = "/".join(["items", collection])

        return self.ApiClient.do_delete(path=path, id=id)

    def get_item_revisions_list(
        self,
        collection: str,
        id: int,
        fields: RequestFields = ["*"],
        limit: int = 100,
        offset: int = 0,
        page: Optional[int] = None,
        sort: List[str] = ["id"],
        single: bool = False,
        filter: dict = {},
        q: Optional[str] = None,
        meta: RequestMeta = [],
    ) -> Tuple[List[Revision], ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#list-item-revisions

        If page is set, offset is not taken into account

        If single, only return first corresponding result from list

        Returns
        -------
            (List of revision, Metadata)
        """
        path = "/".join(["items", collection, str(id), "revisions"])

        params = {
            "fields": ",".join(fields),
            "limit": limit,
            "offset": offset,
            "sort": ",".join(sort),
            "single": single,
            "filter": filter,
            "q": q,
        }

        if page:
            params["page"] = page
            del params["offset"]

        response_data, response_meta = self.ApiClient.do_get(
            path, params=params, meta=meta
        )

        return list(response_data), response_meta

    def get_item_revision(
        self,
        collection: str,
        id: int,
        offset: int,
        fields: RequestFields = ["*"],
        meta: RequestMeta = [],
    ) -> Tuple[Revision, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#retrieve-an-item-revision

        Returns
        -------
            (revision, Metadata)
        """
        path = "/".join(["items", collection, str(id), "revisions", str(offset)])

        params = {"fields": ",".join(fields)}

        return self.ApiClient.do_get(path, params=params, meta=meta)

    def revert_item_revision(
        self,
        collection: str,
        id: int,
        revision: int,
        fields: RequestFields = ["*"],
        meta: RequestMeta = [],
    ) -> Tuple[Revision, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/items.html#revert-to-a-given-revision

        Returns
        -------
            (Item, Metadata)
        """
        path = "/".join(["items", collection, str(id), "revert"])

        params = {"fields": ",".join(fields)}

        return self.ApiClient.do_patch(path=path, id=revision, params=params, meta=meta)

    """

    Files
    https://docs.directus.io/api/files.html

    """

    def get_files_list(
        self,
        fields: RequestFields = ["*"],
        limit: int = 100,
        offset: int = 0,
        sort: List[str] = ["id"],
        single: bool = False,
        filter: dict = {},
        status: Optional[str] = None,
        q: Optional[str] = None,
        meta: RequestMeta = [],
    ) -> Tuple[List[File], ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/files.html#list-the-files

        If single, only return first corresponding result from list

        Returns
        -------
            (List of File, Metadata)
        """
        path = "files"

        params = {
            "fields": ",".join(fields),
            "limit": limit,
            "offset": offset,
            "sort": ",".join(sort),
            "single": single,
            "filter": filter,
            "status": status,
            "q": q,
        }

        # reassemble filter parameter key-value pairs in API format
        for k, v in params.pop("filter").items():
            params["filter{}".format(k)] = v

        response_data, response_meta = self.ApiClient.do_get(
            path, params=params, meta=meta
        )

        return list(response_data), response_meta

    def get_file(
        self, id: int, fields: RequestFields = ["*"], meta: RequestMeta = []
    ) -> Tuple[File, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/files.html#retrieve-a-file

        Returns
        -------
            (File, Metadata)
        """
        path = "/".join(["files", str(id)])

        params = {"fields": ",".join(fields)}

        return self.ApiClient.do_get(path=path, params=params, meta=meta)

    def create_file(
        self,
        data: str,
        filename_download: Optional[str] = None,
        filename_disk: Optional[str] = None,  # required for base64 string of file data
        title: Optional[str] = None,
        description: Optional[str] = None,
        location: Optional[str] = None,
        tags: Optional[str] = None,
        metadata: Optional[str] = None,
        meta: RequestMeta = [],
    ) -> Tuple[File, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/files.html#create-a-file

        Returns
        -------
            (File, Metadata)
        """
        path = "files"

        params = {"data": data}

        if filename_download:
            params["filename_download"] = filename_download
        if filename_disk:
            params["filename_disk"] = filename_disk
        if title:
            params["title"] = title
        if description:
            params["description"] = description
        if location:
            params["location"] = location
        if tags:
            params["tags"] = tags
        if metadata:
            params["metadata"] = metadata

        return self.ApiClient.do_post(path=path, data=params, meta=meta)

    """

    Mail
    https://docs.directus.io/api/mail.html

    """

    def send_email(
        self,
        send_to: List[str],
        subject: str,
        body: str,
        type: Optional[str] = "txt",
        data: Optional[dict] = {},
    ) -> Tuple[dict, ResponseMeta]:
        """
        Find out more: https://docs.directus.io/api/mail.html#send-an-email

        Returns
        -------
            (Empty body, Metadata)
        """
        path = "mail"
        params = {
            "to": send_to,
            "subject": subject,
            "body": body,
            "type": type,
            "data": data,
        }
        return self.ApiClient.do_post(path=path, data=params)
