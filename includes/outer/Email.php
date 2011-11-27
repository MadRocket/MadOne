<?
/**
*	Обертка к классу PHPMailer
*/
require_once( dirname( __FILE__ )."/PHPMailer/class.phpmailer.php" );

class Outer_Email {

	/**
	*	Получение объекта PHPMailer, аналог new PHPMailer().
	*	Устанавливает некоторые нужные настройки по умолчанию.
	*/
	static function create( $exceptions = true ) {
		// Создаем объект, ошибки генерируют исключения
		$mailer = new PHPMailer( $exceptions );
		
		// Кодировка сообщения — UTF-8!
		$mailer->CharSet = 'utf-8';
		
		// Отправитель — noreply с домена сайта; имя — название сайта
		$mailer->From = "noreply@{$_SERVER['HTTP_HOST']}";
		$mailer->FromName = Madone_Config::$i->{'site_title'};
		
		return $mailer;
	}
}

?>