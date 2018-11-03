<?php

namespace Directus\Task;

use Interop\Queue\PsrMessage;
use Interop\Queue\PsrConsumer;
use Enqueue\Dbal\DbalConnectionFactory;

/**
 * Task
 *
 * This is the task listener interface
 *
 */
class Task
{
	/**
	 *
	 * @return Interop\Queue\PsrConnectionFactory
	 */
	public static function getFactory($database = null, $is_cron = false){

		if(!$database){

			$basePath =  realpath(__DIR__ . '/../../../../');
			$configPath = $basePath . '/config';
			$configFilePath = $configPath . '/api.php';

			$config = require $configFilePath;
			$database = $config['database'];
		}

		return new DbalConnectionFactory(
			sprintf('%s://%s:%s@%s:%s/%s',
				$database['type'],
				$database['username'],
				$database['password'],
				$database['host'],
				$database['port'],
				$database['name']
			)
		);
	}

	/**
	 *
	 * @return Interop\Queue\PsrContext
	 */
	public static function getContext($database = null, $is_cron = false){

		return self::getFactory($database, $is_cron)->createContext();
	}

	/**
	 *
	 * @return void
	 */
	public static function dispatch($queue_name, $task_name, $properties = null, $headers = null){

		$context = self::getFactory()->createContext(); // context

		$context->createProducer()->send( // send
			$context->createQueue($queue_name), // queue
			$context->createMessage($task_name, $properties, $headers) // message
		);
	}
}
