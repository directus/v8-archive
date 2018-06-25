<?php

namespace Directus\Util\Installation;

use Directus\Application\Application;
use Directus\Bootstrap;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Zend\Db\TableGateway\TableGateway;

class InstallerUtils
{
    /**
     * Create a config and configuration file into $path
     * @param $data
     * @param $path
     */
    public static function createConfig($data, $path)
    {
        $requiredAttributes = ['db_host', 'db_name', 'db_user', 'db_password'];
        if (!ArrayUtils::contains($data, $requiredAttributes)) {
            throw new \InvalidArgumentException(
                'Creating config files required: ' . implode(', ', $requiredAttributes)
            );
        }

        static::createConfigFile($data, $path);
    }

    public static function createConfigFileContent($data)
    {
        $configStub = file_get_contents(__DIR__ . '/stubs/config.stub');

        return static::replacePlaceholderValues($configStub, $data);
    }

    /**
     * Create config file into $path
     * @param array $data
     * @param string $path
     */
    protected static function createConfigFile(array $data, $path)
    {
        $data = ArrayUtils::defaults([
            'directus_email' => 'admin@example.com',
            'feedback_token' => sha1(gmdate('U') . StringUtils::randomString(32)),
            'feedback_login' => true,
            'cors_enabled' => false
        ], $data);

        $configStub = static::createConfigFileContent($data);

        $configPath = rtrim($path, '/') . '/api.php';
        file_put_contents($configPath, $configStub);
    }

    /**
     * Replace placeholder wrapped by {{ }} with $data array
     * @param string $content
     * @param array $data
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

    /**
     * Create Directus Tables from Migrations
     * @param string $directusPath
     * @throws \Exception
     */
    public static function createTables($directusPath)
    {
        $config = static::getMigrationConfig($directusPath);

        $manager = new Manager($config, new StringInput(''), new NullOutput());
        $manager->migrate('development');
        $manager->seed('development');
    }

    /**
     * Run the migration files
     *
     * @param $directusPath
     */
    public static function runMigration($directusPath)
    {
        $config = static::getMigrationConfig($directusPath);

        $manager = new Manager($config, new StringInput(''), new NullOutput());
        $manager->migrate('development');
    }

    /**
     * Run the seeder files
     *
     * @param $directusPath
     */
    public static function runSeeder($directusPath)
    {
        $config = static::getMigrationConfig($directusPath);

        $manager = new Manager($config, new StringInput(''), new NullOutput());
        $manager->seed('development');
    }

    /**
     * Add Directus default settings
     *
     * @param array $data
     * @param string $directusPath
     *
     * @throws \Exception
     */
    public static function addDefaultSettings($data, $directusPath)
    {
        $directusPath = rtrim($directusPath, '/');
        /**
         * Check if configuration files exists
         * @throws \InvalidArgumentException
         */
        static::checkConfigurationFile($directusPath);

        // require_once $directusPath . '/api/config.php';

        $app = new Application($directusPath, require $directusPath . '/config/api.php');
        // $db = Bootstrap::get('ZendDb');
        $db = $app->getContainer()->get('database');

        $defaultSettings = static::getDefaultSettings($data);

        $tableGateway = new TableGateway('directus_settings', $db);
        foreach ($defaultSettings as $setting) {
            $tableGateway->insert($setting);
        }
    }

    /**
     * Add Directus default user
     *
     * @param array $data
     * @return array
     */
    public static function addDefaultUser($data, $directusPath)
    {
        $app = new Application($directusPath, require $directusPath . '/config/api.php');
        // $db = Bootstrap::get('ZendDb');
        $db = $app->getContainer()->get('database');
        $tableGateway = new TableGateway('directus_users', $db);

        $data = ArrayUtils::defaults([
            'directus_email' => 'admin@example.com',
            'directus_password' => 'password',
            'directus_token' => 'admin_token'
        ], $data);

        $hash = password_hash($data['directus_password'], PASSWORD_DEFAULT, ['cost' => 12]);

        if (!isset($data['directus_token'])) {
            $data['directus_token'] = StringUtils::randomString(32);
        }

        $tableGateway->insert([
            'status' => 1,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => $data['directus_email'],
            'password' => $hash,
            'token' => $data['directus_token'],
            'locale' => 'en-US'
        ]);

        $userRolesTableGateway = new TableGateway('directus_user_roles', $db);

        $userRolesTableGateway->insert([
            'user' => $tableGateway->getLastInsertValue(),
            'role' => 1
        ]);

        return $data;
    }

    /**
     * Check if the given name is schema template.
     * @param $name
     * @param $directusPath
     * @return bool
     */
    public static function schemaTemplateExists($name, $directusPath)
    {
        $directusPath = rtrim($directusPath, '/');
        $schemaTemplatePath = $directusPath . '/api/migrations/templates/' . $name;

        if (!file_exists($schemaTemplatePath)) {
            return false;
        }

        $isEmpty = count(array_diff(scandir($schemaTemplatePath), ['..', '.'])) > 0 ? false : true;
        if (is_readable($schemaTemplatePath) && !$isEmpty) {
            return true;
        }

        return false;
    }

    /**
     * Install the given schema template name
     *
     * @param $name
     * @param $directusPath
     *
     * @throws \Exception
     */
    public static function installSchema($name, $directusPath)
    {
        $directusPath = rtrim($directusPath, '/');
        $templatePath = $directusPath . '/api/migrations/templates/' . $name;
        $sqlImportPath = $templatePath . '/import.sql';

        if (file_exists($sqlImportPath)) {
            static::installSchemaFromSQL(file_get_contents($sqlImportPath));
        } else {
            static::installSchemaFromMigration($name, $directusPath);
        }
    }

    /**
     * Executes the template migration
     *
     * @param $name
     * @param $directusPath
     *
     * @throws \Exception
     */
    public static function installSchemaFromMigration($name, $directusPath)
    {
        $directusPath = rtrim($directusPath, '/');

        /**
         * Check if configuration files exists
         * @throws \InvalidArgumentException
         */
        static::checkConfigurationFile($directusPath);

        // TODO: Install schema templates
    }

    /**
     * Execute a sql query string
     *
     * NOTE: This is not recommended at all
     *       we are doing this because we are trained pro
     *       soon to be deprecated
     *
     * @param $sql
     *
     * @throws \Exception
     */
    public static function installSchemaFromSQL($sql)
    {
        $dbConnection = Application::getInstance()->fromContainer('database');

        $dbConnection->execute($sql);
    }

    /**
     * @param $directusPath
     *
     * @return Config
     */
    private static function getMigrationConfig($directusPath)
    {
        $directusPath = rtrim($directusPath, '/');
        /**
         * Check if configuration files exists
         *
         * @throws \InvalidArgumentException
         */
        static::checkConfigurationFile($directusPath);

        $configPath = $directusPath . '/config';

        $apiConfig = require $configPath . '/api.php';
        $configArray = require $configPath . '/migrations.php';
        $configArray['paths']['migrations'] = $directusPath . '/migrations/db/schemas';
        $configArray['paths']['seeds'] = $directusPath . '/migrations/db/seeds';
        $configArray['environments']['development'] = [
            'adapter' => ArrayUtils::get($apiConfig, 'database.type'),
            'host' => ArrayUtils::get($apiConfig, 'database.host'),
            'port' => ArrayUtils::get($apiConfig, 'database.port'),
            'name' => ArrayUtils::get($apiConfig, 'database.name'),
            'user' => ArrayUtils::get($apiConfig, 'database.username'),
            'pass' => ArrayUtils::get($apiConfig, 'database.password'),
            'charset' => ArrayUtils::get($apiConfig, 'database.charset', 'utf8')
        ];

        return new Config($configArray);
    }

    /**
     * Get Directus default settings
     * @param $data
     * @return array
     */
    private static function getDefaultSettings($data)
    {
        return [
            [
                'scope' => 'global',
                'key' => 'auto_sign_out',
                'value' => '60'
            ],
            [
                'scope' => 'global',
                'key' => 'project_name',
                'value' => isset($data['directus_name']) ? $data['directus_name'] : 'Directus'
            ],
            [
                'scope' => 'global',
                'key' => 'default_limit',
                'value' => '200'
            ],
            [
                'scope' => 'global',
                'key' => 'logo',
                'value' => ''
            ],
            [
                'scope' => 'files',
                'key' => 'file_naming',
                'value' => 'file_id'
            ],
            [
                'scope' => 'files',
                'key' => 'youtube_api_key',
                'value' => ''
            ]
        ];
    }

    /**
     * Check if config and configuration file exists
     * @param $directusPath
     * @throws \Exception
     */
    private static function checkConfigurationFile($directusPath)
    {
        $directusPath = rtrim($directusPath, '/');
        if (!file_exists($directusPath . '/config/api.php')) {
            throw new \Exception('Config file does not exists, run [directus config]');
        }

        if (!file_exists($directusPath . '/config/migrations.php')) {
            throw new \Exception('Migration configuration file does not exists');
        }
    }
}
