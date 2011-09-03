<?php
// require_once($_SERVER['DOCUMENT_ROOT']."/includes/3rdparty/dompdf/dompdf_config.inc.php");
# Функции автоматической загрузки классов во время выполнения (autoload)

# Тут нужно прописать полные пути к каталогам, в которых можно найти инклудные файлы
$include_paths = array
(
    "{$_SERVER['DOCUMENT_ROOT']}/includes",
    "{$_SERVER['DOCUMENT_ROOT']}/includes/applications",
    "{$_SERVER['DOCUMENT_ROOT']}/includes/module",
);

# Исключение для обработки ошибок загрузки класса
class AutoloadException extends Exception { }

# Обработчик автолоада
class AutoloadClass {
    public static function autoload( $classname ) {
        if(mb_strpos($classname, '_') !== false) {
            $classparts = explode('_', strtolower($classname) );
            array_pop($classparts);
            $path = join(DIRECTORY_SEPARATOR, $classparts) . DIRECTORY_SEPARATOR . $classname . ".php";
            
            include_once($path);
        }
        else {
            include_once( $classname . '.class.php' );
        }

        if( class_exists( $classname ) || interface_exists( $classname ) ) {
            // Если у класса есть метод init -  вызовем его для статической инициализации
            if( method_exists( $classname, 'init' ) ) {
                call_user_func( array( $classname, 'init' ) );
            }

            // Если у класса есть метод destruct - запланируем его вызов при завершении скрипта
            if( method_exists( $classname, 'destruct' ) ) {
                register_shutdown_function( array( $classname, 'destruct' ) );
            }

            return;
        }
    }
}

// Активируем наши инклуды
set_include_path( join( PATH_SEPARATOR,  array_merge( $include_paths, array( get_include_path() ) ) ) );

// Активируем автолоад
spl_autoload_register( array('AutoloadClass', 'autoload') );

?>