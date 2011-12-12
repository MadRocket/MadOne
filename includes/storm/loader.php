<?php

/****************************************************************************************************
    Загрузчик Storm, содержит конфигурацию системы. Настраивать и править можно и нужно.
*****************************************************************************************************/

loadStorm( array
(
    // Путь к файлам самой библиотеки Storm
    'storm_path'  => "$_SERVER[DOCUMENT_ROOT]/includes/storm",

    /* Список моделей. Обязательно прописать тут каждую модель проекта, иначе Storm их не увидит.
    Указывется или только имя или массив из имени и имени источника данных. Например: 'Car', array( 'Company', 'Companies' ).
    Если указано только имя, источник данных получает имя '{ИмяМодели}s'. Модель Car из примера получит источник данных Cars. */
    'models' => array
    (
        'Model_Module',
		'Model_Pagetype',
        'Model_Page',
        'Model_Textblock',
        'Model_User',
        array( 'Model_News', 'Model_Newslist' ),
//		'Model_Gallerysection',
		'Model_Galleryimage',
//		'Model_Showcasesection',
		'Model_Showcaseitem',
		'Model_Feedbackmessage',
		'Model_Tempimage',
		'Model_Tempfile',
		'Model_Showcaseimage',
		'Model_Subscriptionrecipient',
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

$_storm_loaded = false;

function loadStorm( $options )
{
	global $_storm_loaded;
	
    if( $_storm_loaded ) return;

    // Настраиваем конфигурацию
    Storm_Config::$db_backend   = "Storm_Db_Connection_".Madone_Config::$Db['db_backend'];
    Storm_Config::$db_mapper    = "Storm_Db_Mapper_".Madone_Config::$Db['db_backend'];
    Storm_Config::$db_host      = Madone_Config::$Db['db_host'];
    Storm_Config::$db_port      = Madone_Config::$Db['db_port'];
    Storm_Config::$db_name      = Madone_Config::$Db['db_name'];
    Storm_Config::$db_user      = Madone_Config::$Db['db_user'];
    Storm_Config::$db_password  = Madone_Config::$Db['db_password'];
    Storm_Config::$db_charset   = Madone_Config::$Db['db_charset'];
    Storm_Config::$db_prefix    = Madone_Config::$Db['db_prefix'];
    Storm_Config::$db_debug     = Madone_Config::$Db['db_debug'];
	
	// Доступные локали
    Storm_Config::$locales = $options['locales'];

    // Модели
    Storm_Config::$models = $options['models'];

    // Регистрируем утилиты, и собственно этим и запускаем Storm в работу
    Storm_Core::getInstance()->registerUtilities();

    $_storm_loaded = true;
}

?>