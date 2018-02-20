<?php

$configFilePath = realpath(__DIR__ . '/../../') . '/config/api.php';

// Creates a simple endpoint to test the server rewriting
// If the server responds "pong" it means the rewriting works
if (!file_exists($configFilePath)) {
    return create_ping_server();
}

$app = new \Directus\Application\Application(realpath(__DIR__ . '/../../'), require $configFilePath);

create_ping_route($app);

return $app;
