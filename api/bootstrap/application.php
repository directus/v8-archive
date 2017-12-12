<?php

$configFilePath = API_PATH . '/config.php';

// Creates a simple endpoint to test the server rewriting
// If the server responds "pong" it means the rewriting works
if (!file_exists($configFilePath)) {
    return create_ping_server();
}

return new \Directus\Application\Application(realpath(__DIR__ . '/../../'), require $configFilePath);
