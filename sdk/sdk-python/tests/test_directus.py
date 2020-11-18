# -*- coding: utf-8 -*-

from json import loads, dumps

from pytest import raises
from responses import POST, add_callback
from responses import activate as activate_responses

from directus.directus import DirectusClient
from directus.exceptions import DirectusException


class TestAuthentication:
    def test_authentication_no_server(self):
        with raises(DirectusException):
            client = DirectusClient()

    def test_authentication_no_project(self):
        with raises(DirectusException):
            client = DirectusClient(url="http://test.local")

    def test_authentication_no_login(self):
        with raises(DirectusException):
            DirectusClient(url="http://test.local", password="password")

    def test_authentication_no_password(self):
        with raises(DirectusException):
            DirectusClient(url="http://test.local", email="email@example.com")

    @activate_responses
    def test_authentication_with_server_and_credentials(self):
        def request_callback(request):
            payload = loads(request.body)
            if (
                payload.get("email") != "email@example.com"
                or payload.get("password") != "password"
            ):
                return (
                    404,
                    {},
                    dumps(
                        {"error": {"code": 100, "message": "Invalid user credentials"}}
                    ),
                )

            response_json = {
                "data": {
                    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiZXhwIjoxNTgxMjgwODg4LCJ0eXBlIjoiYXV0aCIsImtleSI6InRvdG8iLCJwcm9qZWN0IjoiXyJ9.3FVk8UQPwdPewBVQeovncAemeWCa7zgm4PXfpjWd6qI",
                    "user": {"first_name": "Admin", "id": "1", "last_name": "User",},
                },
                "public": True,
            }

            return (200, {}, dumps(response_json))

        add_callback(
            POST,
            "http://test.local/_/auth/authenticate",
            callback=request_callback,
            content_type="application/json",
        )

        with raises(DirectusException):
            client_ko = DirectusClient(
                url="http://test.local",
                email="wrong.email@example.com",
                password="password",
                project="_",
            )

        client_ok = DirectusClient(
            url="http://test.local",
            email="email@example.com",
            password="password",
            project="_",
        )
        assert (
            client_ok.ApiClient.token
            == "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiZXhwIjoxNTgxMjgwODg4LCJ0eXBlIjoiYXV0aCIsImtleSI6InRvdG8iLCJwcm9qZWN0IjoiXyJ9.3FVk8UQPwdPewBVQeovncAemeWCa7zgm4PXfpjWd6qI"
        )
