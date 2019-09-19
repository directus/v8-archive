<?php

namespace Directus\Util\Webhooks;


class WebhookUtils
{

    public static function createHookContent($data)
    {
        $configStub = file_get_contents(__DIR__ . '/stubs/config.stub');

        $webHook = [];
        foreach($data as $hook){
            $configStub = static::replacePlaceholderValues($configStub, $hook);
            print_r($configStub); die;
            // array_merge_recursive($webHook,$configStub);
        }

        // Users are allowed to sent {{project}} to be replaced with the project name
        return static::replacePlaceholderValues($configStub, ArrayUtils::pick($data, 'project'));
    }

    /**
     * Replace placeholder wrapped by {{ }} with $data array
     *
     * @param string $content
     * @param array $data
     *
     * @return string
     */
    public static function replacePlaceholderValues($content, $data)
    {
        if (is_array($data)) {
            $data = ArrayUtils::dot($data);
        }

        $content = StringUtils::replacePlaceholder($content, $data);

        return $content;
    }

}
