<?php

namespace Directus\Mail\Adapters;

use Directus\Exception\RuntimeException;
use Directus\Mail\MessageInterface;

class SwiftMailerAdapter extends AbstractMailerAdapter
{
    /**
     * @var \Swift_Transport
     */
    protected $swiftMailer;

    public function __construct(array $config)
    {
        parent::__construct($config);

        if (!$this->config->has('transport')) {
            throw new RuntimeException('Config: Missing mailer transport');
        }

        switch ($config['transport']) {
            case 'smtp':
                $transport = \Swift_SmtpTransport::newInstance(
                    $this->config->get('host'),
                    $this->config->get('port')
                );

                if ($this->config->has('username')) {
                    $transport->setUsername($this->config->get('username'));
                }

                if ($this->config->has('password')) {
                    $transport->setPassword($this->config->get('password'));
                }

                if ($this->config->has('encryption')) {
                    $transport->setEncryption($this->config->get('encryption'));
                }
                break;
            case 'sendmail':
                $transport = \Swift_SendmailTransport::newInstance($this->config->get('sendmail'));
                break;
            case 'mail':
            default:
                $transport = \Swift_MailTransport::newInstance();
                break;
        }

        $this->swiftMailer = \Swift_Mailer::newInstance($transport);
    }

    public function send(MessageInterface $message)
    {
        $swiftMessage = \Swift_Message::newInstance();

        $swiftMessage->setFrom($message->getFrom());

        $bcc = $message->getBcc();
        if ($bcc !== null) {
            $swiftMessage->setBcc($bcc);
        }

        $cc = $message->getCc();
        if ($cc !== null) {
            $swiftMessage->setCc($cc);
        }

        $swiftMessage->setBody(
            $message->getBody(),
            $message->getContentType(),
            $message->getCharset()
        );

        $this->swiftMailer->send($swiftMessage);
    }
}
