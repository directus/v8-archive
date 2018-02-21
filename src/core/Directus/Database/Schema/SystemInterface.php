<?php

namespace Directus\Database\Schema;

final class SystemInterface
{
    const INTERFACE_PRIMARY_KEY = 'primary_key';
    const INTERFACE_STATUS = 'status';
    const INTERFACE_SORTING = 'sort';
    const INTERFACE_DATE_CREATED = 'date_created';
    const INTERFACE_DATE_MODIFIED = 'date_modified';
    const INTERFACE_USER_CREATED = 'user_created';
    const INTERFACE_USER_MODIFIED = 'user_modified';

    /**
     * Checks whether the interface is a system interface
     *
     * @param string $interface
     *
     * @return bool
     */
    public static function isSystem($interface)
    {
        return in_array($interface, [
            static::INTERFACE_PRIMARY_KEY,
            static::INTERFACE_SORTING,
            static::INTERFACE_STATUS,
            static::INTERFACE_DATE_CREATED,
            static::INTERFACE_DATE_MODIFIED,
            static::INTERFACE_USER_CREATED,
            static::INTERFACE_USER_MODIFIED
        ]);
    }

    /**
     * Checks whether the interface is a system date interface
     *
     * @param $interface
     *
     * @return bool
     */
    public static function isSystemDate($interface)
    {
        return in_array($interface, [
            static::INTERFACE_DATE_CREATED,
            static::INTERFACE_DATE_MODIFIED
        ]);
    }
}
