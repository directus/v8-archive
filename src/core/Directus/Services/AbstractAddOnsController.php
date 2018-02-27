<?php

namespace Directus\Services;

abstract class AbstractAddOnsController extends AbstractService
{
    protected $basePath = null;

    protected function all($basePath, array $params = [])
    {
        $addOns = [];

        if (!file_exists($basePath)) {
            return ['data' => $addOns];
        }

        $filePaths = find_directories($basePath);
        foreach ($filePaths as $path) {
            $path .= '/meta.json';

            if (!file_exists($path)) {
                continue;
            }

            $addOnsPath = trim(substr($path, strlen($basePath)), '/');
            $data = ['id' => basename(dirname($addOnsPath))];

            $meta = @json_decode(file_get_contents($path), true);
            if ($meta) {
                unset($meta['id']);
                $data = array_merge($data, $meta);
            }

            $addOns[] = $data;
        }

        return ['data' => $addOns];
    }
}
