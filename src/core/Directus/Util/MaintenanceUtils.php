<?php

namespace Directus\Util;

/**
 * Maintenance utils class
 */
class MaintenanceUtils
{
    private static function flag($basePath)
    {
        return $basePath . '/logs/maintenance';
    }

    public static function status($basePath)
    {
        if (file_exists(self::flag($basePath))) {
            return 'on';
        }
        return 'off';
    }

    public static function on($basePath)
    {
        $flag = self::flag($basePath);
        if (!file_exists($flag)) {
            fopen($flag, "w");
        }
    }

    public static function off($basePath)
    {
        $flag = self::flag($basePath);
        if (file_exists($flag)) {
            unlink($flag);
        }
    }
}
