<?

/**
    Конфигурация storm.
    Записывается сюда стартовым скриптом, используется отсюда всеми частями системы.
*/

class Storm_Config
{
    static public $db_backend;
    static public $db_mapper;
    static public $db_host;
    static public $db_port;
    static public $db_name;
    static public $db_user;
    static public $db_password;
    static public $db_charset;
    static public $db_prefix;
    static public $db_debug;
    static public $dev;

    static public $models;
    
	static public $locales;
}

?>
