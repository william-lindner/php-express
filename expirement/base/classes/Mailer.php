<?php

namespace Teapot;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mailer class really just hooks into PHPMailer with our settings.
 *
 * Example:
 * Mailer::send([
 *      'to'=>['noreply@example.com'],
 *      'from'=>['noreply@example.com'],
 *      'cc'=>['noreply@example.com'],
 *      'bcc'=>['noreply@example.com']
 * ]);
 */

class Mailer
{
    private static $defaultmailSettings = [
        'from'    => 'noreply@apple.com',
        'to'      => 'mdevelopers@apple.com',
        'subject' => 'Oops!',
        'body'    => 'The droids you were looking for were not found. Please move on.',
    ];

    final private function __construct()
    {
        throw new \Exception('Instantiation of this class is not allowed.', 401);
    }

    /**
     * Establishes the default mail settings and auto sends emails using PHPMailer
     *
     * @param array $mailSettings
     * @param bool $forceSending - Overrides the localization that prevents the sending of a message.
     * @return bool
     */
    public static function send($mailSettings = [], $forceSending = false)
    {
        if (!is_array($mailSettings)) {
            throw new \Exception('Mail information needs to be passed in an Array.', 401);
        }

        $mailInstance = new PHPMailer(true);

        if (!$forceSending && config('server.environment') === 'local' || config('server.environment') === 'uat') {
            return $mailInstance;
        }
        $mailSettings += self::$defaultmailSettings;

        if (!is_array($mailSettings['to'])) {
            $mailSettings['to'] = [$mailSettings['to']];
        }

        $mailInstance->isSMTP();
        $mailInstance->SMTPAuth = true;
        $mailInstance->Host     = config('mail.hostname');
        $mailInstance->Username = config('mail.username');
        $mailInstance->Password = config('mail.password');

        $mailInstance->isHTML(true);
        $mailInstance->CharSet = 'UTF-8';
        $mailInstance->setFrom($mailSettings['from']);
        foreach ($mailSettings['to'] as $email):
            if (strpos($email, 'apple.com') === false) {
                $email = self::$defaultmailSettings['to'];
            }
            $mailInstance->addAddress($email);
        endforeach;

        if (isset($mailSettings['cc'])) {
            static::setCC($mailInstance, $mailSettings['cc']);
        }

        $mailInstance->Subject = $mailSettings['subject'];
        $mailInstance->Body    = $mailSettings['body'];
        return $mailInstance->send();
    }

    /**
     * Configures the CC field for PHPMailer. Used only when CC is passed.
     *
     * @param array $cc
     * @return void
     */
    protected static function setCC(PHPMailer $mailInstance, $cc = [])
    {
        if (!is_array($cc)) {
            $cc = [$cc];
        }

        foreach ($cc as $email):
            $mailInstance->addCC($email);
        endforeach;
    }

}
