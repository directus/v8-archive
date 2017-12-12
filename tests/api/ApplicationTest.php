<?php

class RouteClass extends \Directus\Application\Route
{
    public function name()
    {
        echo "xyz";
    }
}

class CallableClass
{
    public function __invoke()
    {
        echo "xyz";
    }
}

// class AServiceProvider implements \Directus\Application\ServiceProviderInterface
// {
//     protected $booted = false;
//     protected $app;
//
//     public function boot(\Directus\Application\Application $app)
//     {
//         $this->booted = true;
//     }
//
//     public function register(\Directus\Application\Application $app)
//     {
//         $this->app = $app;
//     }
//
//     public function getApp()
//     {
//         return $this->app;
//     }
//
//     public function isBooted()
//     {
//         return $this->booted;
//     }
//
//     public function setBooted($booted)
//     {
//         return $this->booted = (bool) $booted;
//     }
// }

class ApplicationTests extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \Slim\Http\Environment::mock(array(
            'SERVER_NAME' => 'getdirectus.com',
            'REQUEST_METHOD' => 'GET',
            'SCRIPT_NAME' => '/foo', //<-- Physical
            'PATH_INFO' => '/bar', //<-- Virtual
            'QUERY_STRING' => 'one=foo&two=bar',
        ));
    }

    public function testApplicationContainer()
    {
        // callable class:method combination
        $app = new \Directus\Application\Application(__DIR__);
        $this->assertInstanceOf(\Directus\Application\Container::class, $app->getContainer());

        $app = new \Directus\Application\Application(__DIR__, ['something' => 2]);
        $config = $app->getConfig();
        $this->assertSame(2, $config->get('something'));
    }

    public function testApp()
    {
        $app = new \Directus\Application\Application(__DIR__);
        $this->assertSame($app, \Directus\Application\Application::getInstance());

        $this->assertInternalType('string', $app->getVersion());
    }

    public function testRun()
    {
        $app = new \Directus\Application\Application(__DIR__);
        // Prepare request and response objects
        $env = \Slim\Http\Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo',
            'REQUEST_METHOD' => 'GET',
        ]);
        $uri = \Slim\Http\Uri::createFromEnvironment($env);
        $headers = \Slim\Http\Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        $req = new \Slim\Http\Request('GET', $uri, $headers, $cookies, $serverParams, $body);
        $res = new \Slim\Http\Response();
        $app->getContainer()['request'] = $req;
        $app->getContainer()['response'] = $res;
        $app->get('/foo', function ($req, $res) {
            echo 'bar';
        });
        ob_start();
        $app->run();
        $resOut = ob_get_clean();
        $this->assertEquals('bar', (string)$resOut);
    }

    public function testHooks()
    {
        $app = new \Directus\Application\Application(__DIR__);
        $object = (object)['called' => false];
        $data = ['updated' => false];

        /** @var \Directus\Hook\Emitter $emitter */
        $emitter = $app->getContainer()->get('hook_emitter');
        $emitter->addAction('test', function ($object) {
            $object->called = true;
        });

        $emitter->addFilter('test', function (\Directus\Hook\Payload $payload) {
            $payload->set('updated', true);

            return $payload;
        });

        $app->triggerAction('test', $object);
        $data = $app->triggerFilter('test', $data);

        $this->assertSame(true, $object->called);
        $this->assertArrayHasKey('updated', $data);
        $this->assertTrue($data['updated']);
    }

    public function testMissingRequirements()
    {
        $app = new \Directus\Application\Application(__DIR__);
        $data = (object)['called' => false];
        $app->setCheckRequirementsFunction(function () {
            return ['error'];
        });
        $app->onMissingRequirements(function () use ($data) {
            $data->called = true;
        });

        $this->assertTrue($data->called);
    }
}
