<?php

namespace Directus\Mail;

use Directus\Application\Application;
use Directus\Exception\RuntimeException;
use Directus\Util\ArrayUtils;
use InvalidArgumentException;
use Swift_Message;

class Mail
{
    protected $mailer = null;
    protected $settings = [];

    public function __construct($mailer, $settings = [])
    {
        $this->mailer = $mailer;
        $this->settings = $settings;
    }

    public function sendMessage($message)
    {
        $this->mailer->send($message);
    }

    public function getViewContent($viewPath, $data)
    {
        $app = Application::getInstance();

        $data = array_merge(['settings' => $this->settings], $data);
        return $app->getContainer()->get('mail_view')->fetch($viewPath, $data);
    }

    public static function send($viewPath, $data, $callback)
    {
        $app = Application::getInstance();
        $config = $app->getConfig();
        $mailer = $app->getContainer()->get('mailer');

        if (!$mailer) {
            throw new RuntimeException('mail_configuration_no_defined');
        }

        $mailSettings = [];
        $settings = $app->getContainer()->get('app_settings');
        foreach ($settings as $setting) {
            $mailSettings[$setting['scope']][$setting['key']] = $setting['value'];
        }

        $instance = new static($mailer, $mailSettings);

        $message = Swift_Message::newInstance();

        // default mail from address
        $mailConfig = $config->get('mail');
        $message->setFrom($mailConfig['from']);

        $bcc = ArrayUtils::get($mailConfig, 'bcc', null);
        if ($bcc !== null) {
            $message->setBcc($bcc);
        }

        $cc =  ArrayUtils::get($mailConfig, 'cc', null);
        if ($cc !== null) {
            $message->setCc($cc);
        }

        call_user_func($callback, $message);

        if ($message->getBody() == null) {
            // Slim Extras View twig act weird on this version
            $viewContent = $instance->getViewContent($viewPath, $data);
            $message->setBody($viewContent, 'text/html');
        }

        $instance->sendMessage($message);
    }
}
