<?php
/**
 * 
 * @author \$Author$
 */
 
class Madone_Router {
    /**
     * https://github.com/chriso/klein.php
     * @param $route
     * @return string
     */
    function compile_route($route) {
        if (preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {
            $match_types = array(
                'i'  => '[0-9]++',
                'a'  => '[0-9A-Za-z]++',
                'h'  => '[0-9A-Fa-f]++',
                '*'  => '.+?',
                '**' => '.++',
                ''   => '[^/]++'
            );
            foreach ($matches as $match) {
                list($block, $pre, $type, $param, $optional) = $match;

                if (isset($match_types[$type])) {
                    $type = $match_types[$type];
                }
                if ($pre === '.') {
                    $pre = '\.';
                }
                //Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                         . ($pre !== '' ? $pre : null)
                         . '('
                         . ($param !== '' ? "?P<$param>" : null)
                         . $type
                         . '))'
                         . ($optional !== '' ? '?' : null);

                $route = str_replace($block, $pattern, $route);
            }
        }
        return "`^$route$`";
    }

    function route($collection, $uri, $object) {
        // GET ? POST
        $reflection = new ReflectionClass($object);

        foreach($collection as $route => $target_method) {
            if( preg_match($this->compile_route($route), $uri, $m) ) {
                if(method_exists($object, $target_method)) {
                    $call_params = array();
                    foreach( $reflection->getMethod($target_method)->getParameters() as $p) {
                        /** @var $p ReflectionParameter */
                        $call_params[$p->name] = $m[$p->name];
                    }

                    return call_user_func_array(array($object, $target_method), $call_params);
                }
                else {
                    throw new Exception("Method {$target_method} doesn't exists in {$reflection->getName()}!");
                }
            }
        }

        return null;
    }
}

?>