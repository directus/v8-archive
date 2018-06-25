<?php

namespace Directus;

use Directus\Application\Application;
use Directus\Filesystem\Thumbnail;

if (!function_exists('is_uploaded_file_okay')) {
    /**
     * Checks whether upload file has not error.
     *
     * @param $error
     *
     * @return bool
     */
    function is_uploaded_file_okay($error)
    {
        return $error === UPLOAD_ERR_OK;
    }
}

if (!function_exists('get_uploaded_file_error')) {
    /**
     * Returns the upload file error message
     *
     * Returns null if there's not error
     *
     * @param $error
     *
     * @return string|null
     */
    function get_uploaded_file_error($error)
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file exceeds max upload size that was specified on the server.';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the max upload size that was specified in the client.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = 'The uploaded file was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = 'No file was uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Missing temporary upload folder';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'A PHP extension stopped the file upload';
                break;
            case UPLOAD_ERR_OK:
                $message = null;
                break;
            default:
                $message = 'Unknown error uploading a file.';
        }

        return $message;
    }
}

if (!function_exists('append_storage_information'))
{
    /**
     * append storage information to one or multiple file items
     *
     * @param array $rows
     *
     * @return array
     */
    function append_storage_information(array $rows)
    {
        if (empty($rows)) {
            return $rows;
        }

        $container = Application::getInstance()->getContainer();
        $thumbnailDimensions = array_filter(
            explode(',', get_directus_setting('thumbnailer', 'dimensions'))
        );

        // Add default size
        array_unshift($thumbnailDimensions, '200x200');

        $config = $container->get('config');
        $fileRootUrl = $config->get('filesystem.root_url');
        $hasFileRootUrlHost = parse_url($fileRootUrl, PHP_URL_HOST);
        $isLocalStorageAdapter = $config->get('filesystem.adapter') == 'local';
        $list = isset($rows[0]);

        if (!$list) {
            $rows = [$rows];
        }

        foreach ($rows as &$row) {
            $storage = [];
            $thumbnailFilenameParts = explode('.', $row['filename']);
            $thumbnailExtension = array_pop($thumbnailFilenameParts);
            $storage['url'] = $storage['full_url'] = $fileRootUrl . '/' . $row['filename'];

            // Add Full url
            if ($isLocalStorageAdapter && !$hasFileRootUrlHost) {
                $storage['full_url'] = get_url($storage['url']);
            }

            // Add Thumbnails
            foreach (array_unique($thumbnailDimensions) as $dimension) {
                if (Thumbnail::isNonImageFormatSupported($thumbnailExtension)) {
                    $thumbnailExtension = Thumbnail::defaultFormat();
                }

                if (!is_string($dimension)) {
                    continue;
                }

                $size = explode('x', $dimension);
                if (count($size) == 2) {
                    $thumbnailUrl = \Directus\get_thumbnail_url($row['filename'], $size[0], $size[1]);
                    $storage['thumbnails'][] = [
                        'full_url' => $thumbnailUrl,
                        'url' => $thumbnailUrl,
                        'dimension' => $dimension,
                        'width' => $size[0],
                        'height' => $size[1]
                    ];
                }
            }

            // Add embed content
            /** @var \Directus\Embed\EmbedManager $embedManager */
            $embedManager = $container->get('embed_manager');
            $provider = isset($row['type']) ? $embedManager->getByType($row['type']) : null;
            $embed = null;
            if ($provider) {
                $embed = [
                    'html' => $provider->getCode($row),
                    'url' => $provider->getUrl($row)
                ];
            }
            $storage['embed'] = $embed;

            $row['storage'] = $storage;
        }

        return $list ? $rows : reset($rows);
    }
}

if (!function_exists('get_thumbnail_url'))
{
    /**
     * Returns a url to the thumbnailer
     *
     * @param string $name
     * @param int $width
     * @param int $height
     * @param string $mode
     * @param string $quality
     *
     * @return string
     */
    function get_thumbnail_url($name, $width, $height, $mode = 'crop', $quality = 'good')
    {
        // width/height/mode/quality/name
        return get_url(sprintf(
            'thumbnail/%d/%d/%s/%s/%s',
            $width, $height, $mode, $quality, $name
        ));
    }
}
