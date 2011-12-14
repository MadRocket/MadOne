<?
/**
 * Прародитель приложений
 */
class Madone_Application {

    protected $routes;
    protected $page;
    protected $uri;

    /**
     * Запуск приложения!
     * @param Model_Page $page соответствующий объект структуры сайта
     * @param string $uri путь к искомой странице _внутри_ приложения.
     * @return bool Возвращает true, если страница обработана этим приложением, false, если страница приложением не обработана.
     */
    function run( Model_Page $page, $uri = '' ) {
        $this->page = $page;
        $this->uri = $uri;

        if(! $this->routes) {
            $this->routes = array('/?' => 'index');
        }
        $router = new Madone_Router();
        return $router->route($this->routes, $uri, $this);
    }

    function index() {

    }

    function render($template, $vars) {
        $templatePath = array();

        $classname = get_class($this);
        $path_parts = preg_split('~_~', $classname);
        array_pop($path_parts);
        array_shift($path_parts);

        $path = join(DIRECTORY_SEPARATOR, $path_parts);
        if(is_dir(__DIR__."/{$path}/template")) {
            $templatePath[] = __DIR__."/{$path}/template";
        }
        $module_name = array_pop($path_parts);
        if(is_dir( "{$_SERVER['DOCUMENT_ROOT']}/inculdes/template/{$module_name}" )) {
            $templatePath[] = "{$_SERVER['DOCUMENT_ROOT']}/inculdes/template/{$module_name}";
        }

        return Madone_Core::twig($templatePath)->loadTemplate($template)->render($vars);
    }
}