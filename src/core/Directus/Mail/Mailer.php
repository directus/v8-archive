<?php

namespace Directus\Mail;

class Mailer
{
    /**
     * @var TransportManager
     */
    protected $transports;

    /**
     * @var \Swift_Mailer[]
     */
    protected $mailers = [];

    public function __construct(TransportManager $transportManager)
    {
        $this->transports = $transportManager;
    }

    /**
     * Creates a new message instance
     *
     * @return Message
     */
    public function createMessage()
    {
        return new Message();
    }

    public function send($view, array $data, \Closure $callback = null)
    {
        $transport = $this->transports->getDefault();
        $message = $this->createMessage();

        // Get global information
        $config = $transport->getConfig();
        if ($config->has('from')) {
            $message->setFrom($config->get('from'));
        }

        if ($config->has('bcc')) {
            $message->setBcc($config->get('bcc'));
        }

        if ($config->has('cc')) {
            $message->setCc($config->get('cc'));
        }

        $content = parse_twig($view, array_merge(
            $data,
            ['api' => ['env' => get_api_env()]]
        ));

        $message->setBody($content, 'text/html');

        if ($callback) {
            call_user_func($callback, $message);
        }

        if (!array_key_exists($transport->getName(), $this->mailers)) {
            $this->mailers[$transport->getName()] = new \Swift_Mailer($transport);
        }

        $swiftMailer = $this->mailers[$transport->getName()];
        $swiftMailer->send($message);
    }
}
