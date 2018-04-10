<?php

if (!function_exists('get_directus_settings')) {
    /**
     * Returns an array of directus settings
     *
     * @return array
     */
    function get_directus_settings()
    {
        $app = \Directus\Application\Application::getInstance();

        return $settings = $app->getContainer()->get('app_settings');
    }
}

if (!function_exists('get_directus_setting')) {
    /**
     * Returns a directus settings by key+scope combo
     *
     * @param string $scope
     * @param string $group
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    function get_directus_setting($scope, $group, $key, $default = null)
    {
        $settings = get_directus_settings();
        $value = $default;

        foreach ($settings as $setting) {
            if ($setting['scope'] == $scope && $setting['group'] == $group && $setting['key'] == $key) {
                $value = $setting['value'];
                break;
            }
        }

        return $value;
    }
}
