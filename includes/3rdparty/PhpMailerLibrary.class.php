<?
/**
*	Обертка к классу PHPMailer
*/

class PhpMailerLibrary {

	/**
	*	Инициализация класса — подгружаем библиотеку PHPMailer.
	*/
	static function init() {
		require_once( dirname( __FILE__ )."/PHPMailer/class.phpmailer.php" );
	}

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
		$mailer->FromName = Config::$i->{'site_title'};
		
		return $mailer;
	}
}

?>