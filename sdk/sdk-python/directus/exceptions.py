# -*- coding: utf-8 -*-


class DirectusException(Exception):
    """
    An Exception raised when the SDK is used incorrectly.
    """

    @property
    def message(self):
        return self.__dict__.get("message", None) or getattr(self, "args")[0]
