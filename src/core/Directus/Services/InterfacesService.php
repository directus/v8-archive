<?php

namespace Directus\Services;

class InterfacesService extends AbstractService
{
    public function findAll(array $params = [])
    {
        $basePath = $this->container->get('path_base');
        $interfacesPath = $basePath . '/public/core/interfaces';
        $interfaces = [];

        if (!file_exists($interfacesPath)) {
            return $interfaces;
        }

        $filePaths = find_directories($interfacesPath);
        foreach ($filePaths as $path) {
            $path .= '/meta.json';

            if (!file_exists($path)) {
                continue;
            }

            $interfacePath = trim(substr($path, strlen($interfacesPath)), '/');
            $data = ['id' => basename(dirname($interfacePath))];

            $meta = @json_decode(file_get_contents($path), true);
            if ($meta) {
                unset($meta['id']);
                $data = array_merge($data, $meta);
            }

            $data = [
                'data' => $data
            ];

            $interfaces[] = $data;
        }

        return $interfaces;
    }
}
