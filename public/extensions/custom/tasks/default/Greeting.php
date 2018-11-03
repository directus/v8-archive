<?php

namespace Directus\Customs\Tasks;

use Interop\Queue\PsrMessage;
use Interop\Queue\PsrConsumer;
use Directus\Task\TaskInterface;

class Greeting implements TaskInterface{

	public function handle(PsrMessage $message, PsrConsumer $consumer){

		echo PHP_EOL . "Properties" . PHP_EOL;
		print_r($message->getProperties());
		echo PHP_EOL . "Headers" . PHP_EOL;
		print_r($message->getHeaders());
		
		// process a message
		
		$consumer->acknowledge($message);
		// $consumer->reject($message);
	}
}
