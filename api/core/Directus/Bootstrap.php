<?php

namespace Directus;

use Directus\Application\Application;
use Directus\Authentication\FacebookProvider;
use Directus\Authentication\GitHubProvider;
use Directus\Authentication\GoogleProvider;
use Directus\Authentication\Social;
use Directus\Authentication\TwitterProvider;
use Directus\Database\TableGateway\DirectusSettingsTableGateway;
use Directus\Database\TableSchema;
use Directus\Debug\Log\Writer;
use Directus\Embed\EmbedManager;
use Directus\Filesystem\Filesystem;
use Directus\Filesystem\FilesystemFactory;
use Directus\Language\LanguageManager;
use Directus\Providers\FilesServiceProvider;
use Directus\Session\Session;
use Directus\Session\Storage\NativeSessionStorage;
use Directus\Util\ArrayUtils;


/**
 * NOTE: This class depends on the constants defined in config.php
 */
class Bootstrap
{
    public static $singletons = [];

    /**
     * Returns the instance of the specified singleton, instantiating one if it
     * doesn't yet exist.
     *
     * @param  string $key The name of the singleton / singleton factory function
     * @param  mixed $arg An argument to be passed to the singleton factory function
     * @param  bool $newInstance return new instance rather than singleton instance (useful for long running scripts to get a new Db Conn)
     *
     * @return mixed The singleton with the specified name
     */
    public static function get($key, $arg = null, $newInstance = false)
    {
        $key = strtolower($key);
        if (!method_exists(__CLASS__, $key)) {
            throw new \InvalidArgumentException('No such factory function on ' . __CLASS__ . ': ' . $key);
        }
        if ($newInstance) {
            return call_user_func(__CLASS__ . '::' . $key, $arg);
        }
        if (!array_key_exists($key, self::$singletons)) {
            self::$singletons[$key] = call_user_func(__CLASS__ . '::' . $key, $arg);
        }
        return self::$singletons[$key];
    }

    /**
     * Does an extension by the given name exist?
     * @param  string $extensionName
     * @return bool
     */
    public static function extensionExists($extensionName)
    {
        $extensions = self::get('extensions');
        return array_key_exists($extensionName, $extensions);
    }

    /**
     * Get all custom endpoints
     * @return array - list of endpoint files loaded
     * @throws \Exception
     */
    public static function getCustomEndpoints()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $endpointsDirectory = APPLICATION_PATH . '/customs/endpoints';

        if (!file_exists($endpointsDirectory)) {
            return [];
        }

        return find_php_files($endpointsDirectory, true);
    }

    /**
     * SINGLETON FACTORY FUNCTIONS
     */

    /**
     * Make Slim app.
     *
     * @return Application
     */
    private static function app()
    {
        // TODO: Temporary, until we get rid of this Bootstrap object
        return Application::getInstance();
        self::requireConstants(['DIRECTUS_ENV', 'APPLICATION_PATH'], __FUNCTION__);
        $loggerSettings = [
            'path' => APPLICATION_PATH . '/api/logs'
        ];

        $templatesPaths = [APPLICATION_PATH . '/api/views/', APPLICATION_PATH . '/templates/'];
        $app = new Application([
            'templates.path' => $templatesPaths[0],
            'mode' => DIRECTUS_ENV,
            'debug' => false,
            'log.enable' => true,
            'log.writer' => new Writer($loggerSettings),
            'view' => new Twig()
        ]);

        $app->container->singleton('session', function () {
            return Bootstrap::get('session');
        });

        $app->container->singleton('socialAuth', function() {
            return Bootstrap::get('socialAuth');
        });

        $socialAuthServices = static::getSocialAuthServices();
        foreach ($socialAuthServices as $name => $class) {
            if (ArrayUtils::has($authConfig, $name)) {
                $config = ArrayUtils::get($authConfig, $name);
                $socialAuth->register(new $class($app, $config));
            }
        }

        // NOTE: Trying to separate the configuration from bootstrap, bit by bit.
        TableSchema::setConfig(static::get('config'));
        $app->register(new FilesServiceProvider());

        $app->container->singleton('filesystem', function() {
            return Bootstrap::get('filesystem');
        });

        $app->container->get('session')->start();

        return $app;
    }

    private static function getSocialAuthServices()
    {
        return [
            'github' => GitHubProvider::class,
            'facebook' => FacebookProvider::class,
            'twitter' => TwitterProvider::class,
            'google' => GoogleProvider::class
        ];
    }

    private static function mailer()
    {
        $config = self::get('config');
        if (!$config->has('mail')) {
            return null;
        }

        $mailConfig = $config->get('mail');
        switch ($mailConfig['transport']) {
            case 'smtp':
                $transport = \Swift_SmtpTransport::newInstance($mailConfig['host'], $mailConfig['port']);

                if (array_key_exists('username', $mailConfig)) {
                    $transport->setUsername($mailConfig['username']);
                }

                if (array_key_exists('password', $mailConfig)) {
                    $transport->setPassword($mailConfig['password']);
                }

                if (array_key_exists('encryption', $mailConfig)) {
                    $transport->setEncryption($mailConfig['encryption']);
                }
                break;
            case 'sendmail':
                $transport = \Swift_SendmailTransport::newInstance($mailConfig['sendmail']);
                break;
            case 'mail':
            default:
                $transport = \Swift_MailTransport::newInstance();
                break;
        }

        $mailer = \Swift_Mailer::newInstance($transport);

        return $mailer;
    }

    private static function schemaManager()
    {
        $app = static::get('app');

        return $app->getContainer()->get('schema_manager');
    }

    private static function filesystem()
    {
        $config = self::get('config');
        return new Filesystem(FilesystemFactory::createAdapter($config['filesystem']));
    }

    /**
     * Scan for extensions.
     * @return  array
     */
    private static function extensions()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $extensions = [];
        $extensionsDirectory = APPLICATION_PATH . '/customs/extensions/';

        if (!file_exists($extensionsDirectory)) {
            return $extensions;
        }

        foreach (new \DirectoryIterator($extensionsDirectory) as $file) {
            if ($file->isDot()) {
                continue;
            }
            $extensionName = $file->getFilename();

            // Ignore all extensions prefixed with an underscore
            if ($extensionName[0] == '_') {
                continue;
            }

            if (is_dir($extensionsDirectory . $extensionName)) {
                $extensions[$extensionName] = "extensions/$extensionName/main";
            }
        }
        return $extensions;
    }

    /**
     * Scan for interfaces.
     * @return  array
     */
    private static function interfaces()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $uiBasePath = APPLICATION_PATH . '/customs';
        $uiDirectory = $uiBasePath . '/interfaces';
        $uis = [];

        if (!file_exists($uiDirectory)) {
            return $uis;
        }

        $filePaths = find_directories($uiDirectory);
        foreach ($filePaths as $path) {
            $path .= '/component.js';
            if (!file_exists($path)) {
                continue;
            }

            $uiPath = trim(substr($path, strlen($uiBasePath)), '/');
            $uis[] = substr($uiPath, 0, -3);
        }

        return $uis;
    }


    /**
     * Scan for listviews.
     * @return  array
     */
    private static function listViews()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $listViews = [];
        $listViewsDirectory = APPLICATION_PATH . '/customs/listviews/';

        if (!file_exists($listViewsDirectory)) {
            return $listViews;
        }

        foreach (new \DirectoryIterator($listViewsDirectory) as $file) {
            if ($file->isDot()) {
                continue;
            }
            $listViewName = $file->getFilename();
            if (is_dir($listViewsDirectory . $listViewName)) {
                $listViews[] = "listviews/$listViewName/ListView";
            }
        }
        return $listViews;
    }

    /**
     * @return \Directus\Language\LanguageManager
     */
    private static function languagesManager()
    {
        $languages = get_locales_filename();

        return new LanguageManager($languages);
    }

    private static function session()
    {
        return new Session(new NativeSessionStorage());
    }

    private static function socialAuth()
    {
        return new Social();
    }
}
