<?php

namespace Directus\Customs\Tasks;

use Interop\Queue\PsrMessage;
use Interop\Queue\PsrConsumer;
use Directus\Task\TaskInterface;

class GenerateGeneralReport implements TaskInterface{

	public function handle(PsrMessage $message, PsrConsumer $consumer){

		echo PHP_EOL . "Body" . PHP_EOL;
		print_r($message->getBody());
		echo PHP_EOL . "Properties" . PHP_EOL;
		print_r($message->getProperties());
		echo PHP_EOL . "Headers" . PHP_EOL;
		print_r($message->getHeaders());
		
		// process a message

		// 1/0; // any errors will be catched by the job consumer
		
		$consumer->acknowledge($message);
		// $consumer->reject($message);
	}	
}
