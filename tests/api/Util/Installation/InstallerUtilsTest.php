<?php

use Directus\Util\Installation\InstallerUtils;

class InstallerUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testCreateFileException()
    {
        $this->setExpectedException('InvalidArgumentException');
        InstallerUtils::createConfig(__DIR__ . '/', []);
    }

    public function testVariableReplacement()
    {
        $result = InstallerUtils::replacePlaceholderValues('{{name}}', ['name' => 'John']);
        $this->assertSame($result, 'John');

        $result = InstallerUtils::replacePlaceholderValues('{{user.name}}', [
            'user' => [
                'name' => 'John'
            ]
        ]);
        $this->assertSame($result, 'John');

        $result = InstallerUtils::replacePlaceholderValues('{{user.country.name}}', [
            'user' => [
                'name' => 'John',
                'country' => [
                    'name' => 'yes'
                ]
            ]
        ]);
        $this->assertSame($result, 'yes');
    }

    public function testCreateFiles()
    {
        InstallerUtils::createConfig(__DIR__ . '/', [
            'db_type' => 'mysql',
            'db_port' => 3306,
            'db_host' => 'localhost',
            'db_name' => 'directus',
            'db_user' => 'root',
            'db_password' => 'password',
            'mail_from' => 'admin@directus.local',
            'feedback_token' => 'token',
            'feedback_login' => true,
            'auth_secret' => 'secret-auth-key',
            'cors_enabled' => true
        ]);

        $this->assertSame(sha1_file(__DIR__ . '/mock/config.sample.php'), sha1_file(__DIR__ . '/config/api.php'));
    }

    public function testCreateFiles2()
    {
        $this->tearDown();

        InstallerUtils::createConfig(__DIR__ . '/', [
            'env' => 'prod',
            'db_type' => 'mysql',
            'db_port' => 3306,
            'db_host' => 'localhost',
            'db_name' => 'directus',
            'db_user' => 'root',
            'db_password' => 'password',
            'feedback_token' => 'token',
            'feedback_login' => true,
            'auth_secret' => 'secret-auth-key',
            'cors_enabled' => true
        ]);

        $this->assertSame(sha1_file(__DIR__ . '/mock/config.sample2.php'), sha1_file(__DIR__ . '/config/api.prod.php'));
    }

    public function tearDown()
    {
        if (file_exists(__DIR__ . '/config/api.php')) {
            unlink(__DIR__ . '/config/api.php');
        }

        if (file_exists(__DIR__ . '/config/api.prod.php')) {
            unlink(__DIR__ . '/config/api.prod.php');
        }
    }
}
