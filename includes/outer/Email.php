<?
/**
*	Обертка к классу PHPMailer
*/
require_once( dirname( __FILE__ )."/PHPMailer/class.phpmailer.php" );

class Outer_Email {

    /**
     * Получение объекта
     * Устанавливает некоторые нужные настройки по умолчанию.
     * @static
     * @param bool $exceptions
     * @return PHPMailer
     */
	static function create( $exceptions = true ) {
		// Создаем объект, ошибки генерируют исключения
		$mailer = new PHPMailer( $exceptions );
		
		// Кодировка сообщения — UTF-8
		$mailer->CharSet = 'utf-8';
		
		// Отправитель — noreply с домена сайта; имя — название сайта
		$mailer->From = "noreply@{$_SERVER['HTTP_HOST']}";
		$mailer->FromName = Madone_Config::getInstance()->{'site_title'};
		
		return $mailer;
	}

    static function send($subject, $from, $to, $body) {
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

?>