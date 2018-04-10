<?php

if (!function_exists('send_email')) {
    /**
     * Sends a new email
     *
     * @param string $viewPath
     * @param array $data
     * @param callable $callback
     */
    function send_email($viewPath, array $data, callable $callback)
    {
        $app = \Directus\Application\Application::getInstance();
        /** @var Directus\Mail\Mailer $mailer */
        $mailer = $app->getContainer()->get('mailer');

        $mailer->send($viewPath, $data, $callback);
    }
}

if (!function_exists('parse_twig')) {
    /**
     * Parse twig view
     *
     * @param string $viewPath
     * @param array $data
     *
     * @return string
     */
    function parse_twig($viewPath, array $data)
    {
        $app = \Directus\Application\Application::getInstance();

        $mailSettings = [];
        $settings = $app->getContainer()->get('app_settings');
        foreach ($settings as $setting) {
            $mailSettings[$setting['scope']][$setting['group']][$setting['key']] = $setting['value'];
        }

        $data = array_merge(['settings' => $mailSettings], $data);

        return $app->getContainer()->get('mail_view')->fetch($viewPath, $data);
    }
}

if (!function_exists('send_reset_password_email')) {
    /**
     * Sends a new password email
     *
     * @param $user
     * @param string $password
     */
    function send_reset_password_email($user, $password)
    {
        $data = ['new_password' => $password];
        send_email('reset-password.twig', $data, function (\Directus\Mail\Message $message) use ($user) {
            $message->setSubject(
                sprintf('New Temporary Password: %s', get_directus_setting('global', 'project', 'name', ''))
            );
            $message->setTo($user['email']);
        });
    }
}

if (!function_exists('send_forgot_password_email')) {
    /**
     * Sends a new reset password email
     *
     * @param $user
     * @param string $token
     */
    function send_forgot_password_email($user, $token)
    {
        $data = ['reset_token' => $token];
        send_email('forgot-password.twig', $data, function (\Directus\Mail\Message  $message) use ($user) {
            $message->setSubject(
                sprintf('Password Reset Request: %s', get_directus_setting('global', 'project', 'name', ''))
            );
            $message->setTo($user['email']);
        });
    }
}

if (!function_exists('send_new_install_email')) {
    /**
     * Sends a new installation email
     *
     * @param array $data
     */
    function send_new_install_email(array $data)
    {
        send_email('new-install.twig', $data, function (\Directus\Mail\Message $message) use ($data) {
            $message->setSubject(
                sprintf('Your New Instance: %s', get_directus_setting('global', 'project', 'name', ''))
            );
            $message->setTo($data['user']['email']);
        });
    }
}

if (!function_exists('send_user_invitation_email')) {
    /**
     * Sends a invitation email
     *
     * @param string $email
     * @param string $token
     */
    function send_user_invitation_email($email, $token)
    {
        $data = ['token' => $token];
        send_email('user-invitation.twig', $data, function (\Directus\Mail\Message $message) use ($email) {
            $message->setSubject(
                sprintf('Invitation to Instance: %s', get_directus_setting('global', 'project', 'name', ''))
            );
            $message->setTo($email);
        });
    }
}
