<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Swift;

use Vero\Config\Config;

/**
 * Swift Mailer factory.
 */
class MailerFactory
{
    /**
     * Create Swift Mailer instance from config.
     * 
     * @param Config $config
     * @return \Swift_Mailer
     */
    public static function create(Config $config)
    {
        switch ($config -> get('mail.transport', 'mail')) {
            case 'smtp':
                $transport = \Swift_SmtpTransport::newInstance(
                    $config -> get('mail.host', 'localhost'),
                    $config -> get('mail.port', 25)
                );

                $transport -> setUsername($config -> get('mail.username'));
                $transport -> setPassword($config -> get('mail.password'));
                
                break;
            case 'sendmail':
                $transport = \Swift_SendmailTransport::newInstance();

                if ($path = $config -> get('mail.path')) {
                    $transport -> setCommand($path);
                }

                break;
            case 'mail':
                $transport = \Swift_MailTransport::newInstance();
        }

        return \Swift_Mailer::newInstance($transport);
    }
}
