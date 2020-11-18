# -*- coding: utf-8 -*-

from json.decoder import JSONDecodeError
from typing import List, Optional, Tuple, Union
from urllib.parse import urljoin
from jwt import decode
from time import time

from requests import Response, request

from ..exceptions import DirectusException
from ..typing import (
    ResponseMeta,
    RequestMeta,
    RequestHeaders,
    RequestData,
    RequestParams,
)


class ApiClient(object):
    def __init__(
        self,
        url: str,
        project: str,
        email: Optional[str] = None,
        password: Optional[str] = None,
    ):
        self.baseHeader = {}
        self.token = ""
        self.url = url
        self.baseUrl = urljoin(url, project)
        self.project = project
        if email and password:
            auth, _ = self.do_post(
                path="auth/authenticate", data={"email": email, "password": password}
            )
            self.token = auth["token"]
            self.baseHeader["authorization"] = f"Bearer {self.token}"

    def do_get(
        self,
        path: str,
        params: RequestParams = {},
        headers: RequestHeaders = {},
        meta: RequestMeta = [],
    ) -> Tuple[dict, ResponseMeta]:
        headers = {**self.baseHeader, **headers}
        params["meta"] = ",".join(meta)
        response = self._make_request(
            "GET", "/".join([self.baseUrl, path]), headers=headers, params=params
        )

        if not response:
            return ({}, {})

        result = response.json()

        return (
            [result["data"]]
            if params.get("single") and params["single"]
            else result["data"],
            result["meta"] if result.get("meta") else {},
        )

    def do_post(
        self,
        path: str,
        data: RequestData,
        headers: RequestHeaders = {},
        meta: RequestMeta = [],
        auth_refresh: bool = False,
    ) -> Tuple[dict, ResponseMeta]:
        headers = {**self.baseHeader, **headers}
        params = {"meta": ",".join(meta)}
        response = self._make_request(
            "POST",
            "/".join([self.baseUrl, path]),
            headers=headers,
            data=data,
            params=params,
            auth_refresh=auth_refresh,
        )

        if not response:
            return ({}, {})

        result = response.json()

        return result["data"], result["meta"] if result.get("meta") else {}

    def do_patch(
        self,
        path: str,
        id: Union[str, int],
        data: RequestData = {},
        params: RequestParams = {},
        headers: RequestHeaders = {},
        meta: RequestMeta = [],
    ) -> Tuple[dict, ResponseMeta]:
        headers = {**self.baseHeader, **headers}
        params["meta"] = ",".join(meta)
        url = "/".join([self.baseUrl, path, str(id)])

        response = self._make_request(
            "PATCH", url, headers=headers, data=data, params=params
        )

        if not response:
            return ({}, {})

        result = response.json()

        return result["data"], result["meta"] if result.get("meta") else {}

    def do_delete(
        self, path: str, id: Union[int, str], headers: RequestHeaders = {}
    ) -> bool:
        headers = {**self.baseHeader, **headers}
        url = "/".join([self.baseUrl, path, str(id)])
        response = self._make_request("DELETE", url, headers=headers)

        if not response:
            return False

        if response.status_code != 204:
            return False

        return True

    def _make_request(
        self,
        method: str,
        url: str,
        headers: RequestHeaders,
        data: RequestData = None,
        params: RequestParams = {},
        auth_refresh: bool = False,
    ) -> Optional[Response]:
        if not auth_refresh:  # don't refresh a refresh
            self._auto_refresh_token()
        response = request(
            method=method, url=url, headers=headers, json=data, params=params
        )

        try:
            if response.json().get("error"):
                raise DirectusException(
                    f"{response.json()['error']['message']} ( Code {response.json()['error']['code']}: Please have a look at https://docs.directus.io/api/errors.html )"
                )
        except JSONDecodeError:
            return None

        return response

    def _auto_refresh_token(self) -> None:
        if self.token:
            decoded_token = decode(self.token, verify=False)

        if self.token and int(decoded_token["exp"] - 60) < int(time()):
            new_token, _ = self.do_post(
                "/".join(["auth", "refresh"]), data={"token": self.token}, auth_refresh=True
            )
            self.token = new_token["token"]
            self.baseHeader["authorization"] = f"Bearer {self.token}"  # reset authorization header
