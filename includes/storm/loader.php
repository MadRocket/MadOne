<?php

/****************************************************************************************************
    Загрузчик Storm, содержит конфигурацию системы. Настраивать и править можно и нужно.
*****************************************************************************************************/

loadStorm( array
(
    // Путь к классам моделей
    'model_paths' => array
    (
        "$_SERVER[DOCUMENT_ROOT]/includes/models",
    ),

    // Путь к файлам самой библиотеки Storm
    'storm_path'  => "$_SERVER[DOCUMENT_ROOT]/includes/storm",

    /* Список моделей. Обязательно прописать тут каждую модель проекта, иначе Storm их не увидит.
    Указывется или только имя или массив из имени и имени источника данных. Например: 'Car', array( 'Company', 'Companies' ).
    Если указано только имя, источник данных получает имя '{ИмяМодели}s'. Модель Car из примера получит источник данных Cars. */
    'models' => array
    (
        'MadoneModule',
        'MadonePage',
        'MadonePageContent',
        
        'MadoneTextBlock',
        'MadoneUser',
        array( 'MadoneNews', 'MadoneNewsList' ),
		'MadoneGallerySection',
		'MadoneGalleryImage',
		'MadoneShowcaseSection',
		'MadoneShowcaseItem',
		'MadoneFeedbackMessage',
		'MadoneTempImage',
		'MadoneTempFile',		
		'MadoneShowcaseImage',
		'MadoneShowcaseMovie',
		'MadoneSubscriptionRecipient',
	),
    
	// Список локалей системы. Должен содержать как минимум одну локаль.
	'locales' => array (
		'ru_RU.UTF-8',
//		'en_US.UTF-8',
//		'de_DE.UTF-8',
    ),

));

/***************************************************************************************************
    Далее — системные части загрузчика, ничего настраиваемого уже нет.
****************************************************************************************************/

// Обработчик автолоада
class StormAutoloadClass
{
    /**
        Выполнение загрузки класса
    */
    public static function autoload( $classname )
    {
        // Подключаем файл, в котором должен лежать наш класс
        include_once( "{$classname}.class.php" );

        //  Удалось ли загрузить класс?
        if( class_exists( $classname ) )
        {
            // Если у класса есть метод init -  вызовем его для статической инициализации
            if( method_exists( $classname, 'init' ) )
            {
                call_user_func( array( $classname, 'init' ), $classname );
            }

            // Если у класса есть метод destruct - запланируем его вызов при завершении скрипта
            if( method_exists( $classname, 'destruct' ) )
            {
                register_shutdown_function( array( $classname, 'destruct' ) );
            }

            // Все в порядке
            return;
        }
    }
}

$_storm_loaded = false;

function loadStorm( $options )
{
	global $_storm_loaded;
	
    if( $_storm_loaded ) return;

    // Выключаем magic_quotes_runtime, чтобы они не портили нам данные из БД
    if( get_magic_quotes_runtime() ) set_magic_quotes_runtime( false );

    // Активируем наши инклуды
    @set_include_path( join( PATH_SEPARATOR,  array_merge( $options['model_paths'], array( $options['storm_path'] ), array( get_include_path() ) ) ) );

    // Активируем автолоад
    spl_autoload_register( array('StormAutoloadClass', 'autoload') );

    // Настраиваем конфигурацию
    StormConfig::$db_backend   = "Storm".Config::$Db['db_backend']."DbConnection";
    StormConfig::$db_mapper    = "Storm".Config::$Db['db_backend']."DbMapper";
    StormConfig::$db_host      = Config::$Db['db_host'];
    StormConfig::$db_port      = Config::$Db['db_port'];
    StormConfig::$db_name      = Config::$Db['db_name'];
    StormConfig::$db_user      = Config::$Db['db_user'];
    StormConfig::$db_password  = Config::$Db['db_password'];
    StormConfig::$db_charset   = Config::$Db['db_charset'];
    StormConfig::$db_prefix    = Config::$Db['db_prefix'];
    StormConfig::$db_debug     = Config::$Db['db_debug'];
	
	// Доступные локали
    StormConfig::$locales = $options['locales'];

    // Модели
    StormConfig::$models = $options['models'];

    // Регистрируем утилиты, и собственно этим и запускаем Storm в работу
    StormCore::getInstance()->registerUtilities();

    $_storm_loaded = true;
}

?>