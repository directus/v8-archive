<?php

namespace Directus;

if (!function_exists('get_max_upload_size')) {
    /**
     * Get the maximum upload size in bytes
     *
     * @return int
     */
    function get_max_upload_size()
    {
        static $maxSize = null;

        if ($maxSize == null) {
            $maxUploadSize = convert_shorthand_size_to_bytes(ini_get('upload_max_filesize'));
            $maxPostSize = convert_shorthand_size_to_bytes(ini_get('post_max_size'));

            return min($maxUploadSize, $maxPostSize);
        }

        return $maxSize;
    }
}
