<?php
require $_SERVER['DOCUMENT_ROOT'].'/vendor/swiftmailer/swiftmailer/lib/swift_required.php';
class Madone_Email {
    static function send($subject, $from, $to, $body) {
        if (! $from) {
            $from = array( Madone_Config::getInstance()->{'site_title'} => "noreply@{$_SERVER['HTTP_HOST']}" );
        }

        $transport = Swift_MailTransport::newInstance();

        // Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance($transport);

        // Create a message
        $message = Swift_Message::newInstance($subject)
          ->setFrom($from)
          ->setTo($to)
          ->setBody($body)
          ;

        // Send the message
        $result = $mailer->send($message);

        return $result;
    }
}
