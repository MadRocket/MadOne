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
     * @param MadonePage $page соответствующий объект структуры сайта
     * @param string $uri путь к искомой странице _внутри_ приложения.
     * @return bool Возвращает true, если страница обработана этим приложением, false, если страница приложением не обработана.
     */
    function run( MadonePage $page, $uri = '' ) {
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

        return Madone::twig(array("{$_SERVER['DOCUMENT_ROOT']}/includes/template/_default", "{$_SERVER['DOCUMENT_ROOT']}/includes/template") )->loadTemplate($template)->render($vars);
    }
}