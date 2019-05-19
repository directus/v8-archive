<?php

namespace Directus\Task;

use Interop\Queue\PsrMessage;
use Interop\Queue\PsrConsumer;

/**
 * Task Interface
 *
 * This is the task listener interface
 *
 */
interface TaskInterface
{
    /**
     * @param Interop\Queue\PsrMessage $message
     * @param Interop\Queue\PsrConsumer $consumer
     *
     * @return void
     */
	public function handle(PsrMessage $message, PsrConsumer $consumer);
}
