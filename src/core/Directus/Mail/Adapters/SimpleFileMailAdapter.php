<?php

namespace Directus\Mail\Adapters;

use Directus\Mail\MessageInterface;

class SimpleFileMailAdapter extends AbstractMailerAdapter
{
    /**
     * @param MessageInterface $message
     *
     * @return mixed
     */
    public function send(MessageInterface $message)
    {
        $path = rtrim($this->config->get('path', ''), '/') . '/' . time() . '.txt';

        $message = [
            $message->getSubject(),
            $message->getBody()
        ];

        file_put_contents($path, implode("\n", $message));
    }
}
