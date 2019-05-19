<?php

// Init
$basePath =  realpath(__DIR__ . '/../../../../');

// Composer Autoloader
require $basePath . '/vendor/autoload.php';

// raw data
$properties = [
	'name' => 'Abdelaziz Elrashed',
	'time' => date('Y-m-d H:i:s'),
];

$headers = [
	uniqid(),
	0 => microtime(1),
];

\Directus\Task\Task::dispatch(
	'default', // queue name
	'HelloWorld', // task name
	$properties, // optional
	$headers // optional
);
