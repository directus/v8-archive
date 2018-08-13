<?php

namespace Directus;

use Directus\Util\StringUtils;

if (!function_exists('thumbnail_add_default')) {
    /**
     * Adds the default thumbnail dimension
     *
     * @param string|array  $thumbnails
     *
     * @return array
     */
    function thumbnail_add_default($thumbnails)
    {
        $defaultDimension = '200x200';
        if ($thumbnails) {
            $dimensions = StringUtils::safeCvs($thumbnails);
        } else {
            $dimensions = [];
        }

        if (!in_array($defaultDimension, $dimensions)) {
            array_unshift($dimensions, $defaultDimension);
        }

        return $dimensions;
    }
}

if (!function_exists('thumbnail_get_supported')) {
    /**
     * Returns Supported thumbnails
     *
     * @return array
     */
    function thumbnail_get_supported()
    {
        return thumbnail_add_default(setting_supported_thumbnails());
    }
}
