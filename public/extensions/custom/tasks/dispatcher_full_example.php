<?php

// Init
$basePath =  realpath(__DIR__ . '/../../../../');
$configPath = $basePath . '/config';
$configFilePath = $configPath . '/api.php';

// Composer Autoloader
require $basePath . '/vendor/autoload.php';

// Load application config to grap database info
$config = require 'config/api.php';

// Create a context to talk to Task Manager
$context = \Directus\Task\Task::getContext($config['database'], true);

// Create or use the queue which specified here
$queue = $context->createQueue('default'); // queue `default` or any other should be exists in the config

// raw data
$properties = [
	'name' => 'Abdelaziz Elrashed',
	'time' => date('Y-m-d H:i:s'),
];

$headers = [
	uniqid(),
	0 => microtime(1),
];

// Create Message for the Task to consume
$message = $context->createMessage(
	// Task Name, case sensitive.
	'HelloWorld', // Should be inside `default` queue config 
				  // and specified with the class that implement TaskInterface
	$properties,
	$headers
);

// Send task to the queue
$context->createProducer()->send($queue, $message);
