<?

/**
 *  Конфигурация сайта
 */

class Madone_Config {
    static public $Db;
	static public $i;
	
	static private $attributes;
    
    static function init() { 
    	self::$i = new Madone_Config;
    
        // Настройки БД        
        self::$Db = array(
		    'db_backend'   => 'Mysql',
		    'db_host'      => 'localhost',
		    'db_port'      => '',
		    'db_name'      => 'Madone_dev2',
		    'db_user'      => 'root',
		    'db_password'  => 'root',
		    'db_charset'   => 'utf8',
		    'db_prefix'    => '',
		    
		    // Отладочный режим БД — полезно для просмотра что и как выбирается
		    'db_debug'    => false
	    );
	    
	    self::readFromFile();
    }
	
	function __get( $name ) {
    	if( array_key_exists($name, self::$attributes) ) {
    		return self::$attributes[$name];
    	} else {
    		throw new Exception("Параметр {$name} отсутствует в конфигурации сайта!");
    	}
    }

    function get( $name ) {
    	if( array_key_exists($name, self::$attributes) ) {
    		return self::$attributes[$name];
    	} else {
    		throw new Exception("Параметр {$name} отсутствует в конфигурации сайта!");
    	}
    }

	function __set( $name, $value ) {
    	if( array_key_exists($name, self::$attributes) ) {
    		self::$attributes[$name] = $value;
    	} else {
    		throw new Exception("Параметр {$name} отсутствует в конфигурации сайта!");
    	}
    }

    function __isset( $name ) {
        return array_key_exists($name, self::$attributes);
    }
    
    static function writeToFile() {
	    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/includes/config.js', json_encode(self::$attributes));
    }
    
    static function readFromFile() {
		self::$attributes = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/includes/config.js'), true);
    }
}

?>