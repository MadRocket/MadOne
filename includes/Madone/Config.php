<?php

/**
 *  Конфигурация сайта
 */

class Madone_Config {
    static public $Db = array(
        'db_backend'   => 'Mysql',
        'db_host'      => 'localhost',
        'db_port'      => '',
        'db_name'      => 'Madone_dev_context_modules',
        'db_user'      => 'root',
        'db_password'  => 'root',
        'db_charset'   => 'utf8',
        'db_prefix'    => '',

        // Отладочный режим БД — полезно для просмотра что и как выбирается
        'db_debug'    => false
    );

    public $site_title = "Тестовый сайт";
    public $admin_email = "rojkov.dmitry@gmail.com";

    /**
     * Список моделей. Обязательно прописать тут каждую модель проекта, иначе Storm их не увидит.
     * Указывется или только имя или массив из имени и имени источника данных. Например: 'Car', array( 'Company', 'Companies' ).
     * Если указано только имя, источник данных получает имя '{ИмяМодели}s'. Модель Car из примера получит источник данных Cars.
     * @var array
     */
    public $models = array (
        'Model_Module',
        'Model_Pagetype',
        'Model_Page',
        'Model_Textblock',
        'Model_User',
        array( 'Model_News', 'Model_Newslist' ),
        'Model_Galleryimage',
        'Model_Showcaseitem',
        'Model_Feedbackmessage',
        'Model_Tempimage',
        'Model_Tempfile',
        'Model_Showcaseimage',
        'Model_Subscriptionrecipient',
    );

    public $locales = array (
        'ru_RU.UTF-8',
//		'en_US.UTF-8',
//		'de_DE.UTF-8',
    );

	static public $instance;
	static private $attributes;

    private function __construct() {

    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
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
//	    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/includes/config.js', json_encode(self::$attributes));
    }
    
    private function readFromFile() {
//		self::$attributes = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/includes/config.js'), true);
    }
}
