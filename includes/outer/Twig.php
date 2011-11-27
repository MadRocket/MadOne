<?php
/**
 * 
 * @author \$Author$
 */

require_once 'Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

class Outer_Twig {
    public static function get($path = null) {
        if($path) {
            $twig =  new Twig_Environment(new Twig_Loader_Filesystem($path));
        }
        else {
            $twig = new Twig_Environment();
        }

        $twig->addExtension(new Twig_Extensions_Extension_Text());
        $twig->addExtension(new Twig_Extensions_Extension_Debug());

        return $twig;
    }
}

?>