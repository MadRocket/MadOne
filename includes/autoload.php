<?php
# Функции автоматической загрузки классов во время выполнения (autoload)
# Тут нужно прописать полные пути к каталогам, в которых можно найти инклудные файлы
$include_paths = array (
    "{$_SERVER['DOCUMENT_ROOT']}/includes"
);

# Исключение для обработки ошибок загрузки класса
class AutoloadException extends Exception { }

# Обработчик автолоада
class AutoloadClass {
    public static function autoload( $classname ) {
        $path = preg_replace('/_/', DIRECTORY_SEPARATOR, $classname);
        if(is_file("{$_SERVER['DOCUMENT_ROOT']}/includes/{$path}.php")) {
            include_once("{$path}.php");
        }

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
    }
}

# Активируем наши инклуды
set_include_path( join( PATH_SEPARATOR,  array_merge( $include_paths, array( get_include_path() ) ) ) );

# Активируем автолоад
spl_autoload_register( array('AutoloadClass', 'autoload') );

?>