<?php
/**
 * 
 * @author \$Author$
 */
 
class Outer_Twig {

    /**
     * @var Twig_Environment
     */
    protected static $twig;

    public static function init() {
        require_once 'Twig/lib/Twig/Autoloader.php';
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'/includes/template');
        self::$twig = new Twig_Environment($loader, array(
//            'cache' => $_SERVER['DOCUMENT_ROOT'].'/includes/cache/template'
        ));
    }

    public static function get($path = null) {
        if($path) {
            $loader = new Twig_Loader_Filesystem($path);
            self::$twig->setLoader($loader);
        }
        return self::$twig;
    }
}

?>