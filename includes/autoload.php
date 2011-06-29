<?php
require_once($_SERVER['DOCUMENT_ROOT']."/includes/3rdparty/dompdf/dompdf_config.inc.php");
# Функции автоматической загрузки классов во время выполнения (autoload)

# Тут нужно прописать полные пути к каталогам, в которых можно найти инклудные файлы
$include_paths = array
(
    "{$_SERVER['DOCUMENT_ROOT']}/includes/applications",
    "{$_SERVER['DOCUMENT_ROOT']}/includes",
    "{$_SERVER['DOCUMENT_ROOT']}/includes/3rdparty",
    "{$_SERVER['DOCUMENT_ROOT']}/includes/module",
);

# Исключение для обработки ошибок загрузки класса
class AutoloadException extends Exception { }

# Обработчик автолоада
class AutoloadClass {
    public static function autoload( $classname ) {
        include_once( $classname . '.class.php' );

        #  Удалось ли загрузить класс?
        if( class_exists( $classname ) || interface_exists( $classname ) ) {
            # Если у класса есть метод init -  вызовем его для статической инициализации
            if( method_exists( $classname, 'init' ) ) {
                call_user_func( array( $classname, 'init' ) );
            }

            # Если у класса есть метод destruct - запланируем его вызов при завершении скрипта
            if( method_exists( $classname, 'destruct' ) ) {
                register_shutdown_function( array( $classname, 'destruct' ) );
            }

            # Все в порядке
            return;
        }

        # Класса нет — генерим класс, выдающий на создание исключение
        // TODO: Заккоментировано потому, что иначе class_exists всегда выдает true, и проверить существует ли класс с его помощью уже не получится
        // TODO: Проверить будет ли это правильно работать если здесь тупо выдавать исключение
/*
        eval("
            class {$classname}
            {
                function __construct()
                {
                    throw new AutoloadException( 'Class {$classname} not found' );
                }

                static function __callstatic( \$m, \$args )
                {
                    throw new AutoloadException( 'Class {$classname} not found' );
                }
            }");
*/
    }
}

# Активируем наши инклуды
set_include_path( join( PATH_SEPARATOR,  array_merge( $include_paths, array( get_include_path() ) ) ) );

# Активируем автолоад
spl_autoload_register( array('AutoloadClass', 'autoload') );

?>