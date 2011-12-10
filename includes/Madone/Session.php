<?

/**
    Сессия пользователя madone
*/

class Madone_Session
{
    static private $session_lifetime = '7200'; // Время жизни сессии в секундах

    private $session = null; // Ссылка на PHP-сессию
    private $user = null;    // текущий пользователь, объект MadoneUser
    
    private $loginAttempt = false;   // Была попытка авторизации
    private $loginSucceeded = false; // Попытка авторизации была успешной 

    /**
     * @var Madone_Session
     */
    private static $instance = null; // Единственный экземпляр класса, который позволительно создать, фишки для singletone

    /**
     * Доступ к объекту класса — синглтону
     * @static
     * @return Madone_Session
     */
    public static function getInstance() {
        if( self::$instance == null ) self::$instance = new Madone_Session();
        return self::$instance;
    }
    
    /**
        Приватный конструктор — извне невозможно сконструировать экземпляр объекта.
        Открытие сессии, проверка и тому подобное
    */
    private function __construct() {
        // Открываем сессию
        if( array_key_exists('PHPSESSID', $_POST) ) {
        	session_id($_POST['PHPSESSID']);
        }
		$started = session_start();			
		
		// Проверим наличие в сессии наших данных, создадим, если нету
		if( ! array_key_exists( 'madone', $_SESSION ) ) {
			$_SESSION['madone'] = array();
		}
        
        // Сохраняем ссылку на элемент глобального массива, чтобы работать уже со своим полем, и при этом модифицировать сессию PHP
        $this->session = & $_SESSION['madone'];

        // Проверяем сессию        
        $this->checkSession();
    }
    
    /**
        Проверика сессии — время жизни, авторизация и т.п.
    */
    private function checkSession() {
        // Проверяем время жизни, если больше заданного — выходим
        if( ! array_key_exists('lastvisit', $this->session) || ! $this->session['lastvisit'] || $this->session['lastvisit'] + self::$session_lifetime < time() )
        {
            $this->logout();
            return false;
        }
        
        // Апдейтим время жизни
        $this->session['lastvisit'] = time();
        
        // Читаем пользователя, если он есть
        $this->user = $this->session['user'] ? unserialize( $this->session['user'] ) : null;

        return $this;
    }
    
    /**
        Авторизация пользователя
    */
    public function login( $name, $password ) {
        $this->loginAttempt = true;
        
        if( $name != '' && $password != '' )
        {
            $this->user = Model_Users( array( 'login' => $name, 'password' => md5( $password ) ) )->follow( 1 )->first();
            $this->session['user'] = $this->user ? serialize( $this->user ) : null;
            $this->session['lastvisit'] = time();
        }
        
        $this->loginSucceeded = (boolean) $this->user;
        
        return $this->loginSucceeded;
    }
    
    /**
        Деавторизация пользователя
    */
    public function logout() {
        $this->session['user'] = null;
        $this->user = null;
        
        return true;
    }

    /**
        Получение текущего пользователя сессии (или null, если не авторизован)
    */    
    public function getUser() {
        return $this->user;
    }
    
    /**
        Получение флага попытки логина
    */
    public function getLoginAttempt() {
        return $this->loginAttempt;
    }
        
    /**
        Получение флага успешности попытки логина
    */
    public function getLoginSucceeded() {
        return $this->loginSucceeded;
    }
    
    /**
        Перечитывание данных пользователя из БД, перезапись сессии
    */
    public function reloadData() {
        $this->user = Model_Users( array( 'login' => $this->user->login ) )->follow( 1 )->first();
        $this->session['user'] = $this->user ? serialize( $this->user ) : null;
    }
    
    /**
    *	Запись/чтение произвольных данных сессии
    */
    public function __set( $name, $value ) {
		$this->session[ $name ] = $value;    
    }

    public function __get( $name ) {
    	return array_key_exists( $name, $this->session ) ? $this->session[$name] : null;
    }

    public function __isset( $name ) {
    	return array_key_exists( $name, $this->session ) ? true : false;
    }
}

?>