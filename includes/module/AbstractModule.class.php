<?
/**
    Прародитель модулей административного интерфейса
*/
class AbstractModule {    
    
    protected $uri;
    protected $cmsUri;
    protected $ajaxUri;
    protected $uriPath;

    /**
    *   Конструктор
    *   $name - имя, под которым модуль будет работать
    */
    function __construct( $name ) {

        // Проверим наличие имени
        if( ! $name ) {
            throw new Exception( "MadoneModule cannot be constructed without a name" );
        }

        // Получим всякие uri от запроса CMS
        $request = new MadoneCmsRequest();
        
        $this->cmsUri   = $request->cmsUri;
        $this->uri      = "{$this->cmsUri}/{$name}";
        $this->ajaxUri  = "{$this->cmsUri}/ajax/{$name}";
        $this->uriPath  = $this->cmsUri . $request->objectName == $name ? "/{$name}".( $request->uri != '/' ? $request->uri : '' ) : "/{$name}";
    }
    
    /**
    *   Получение шаблона модуля с указанным именем.
    *   Равноценно вызову new Template с теми же аргументами за исключением того, что путь к шаблону
    *   будет автоматически модифицирован, а в массив переменых будет добавлен элемент module
    *   Вызов $this->getTemplate( 'index' ) изнутри MadoneMahModule равносилен 
    *   new Template( 'modules/MadoneMahModule/index', array( module = $this ) )
    *   Возвращает объект Template
    */
    function getTemplate( $file, $vars = array() ) {

        foreach( get_object_vars( $this ) as $name => $value ) {
            $vars[ $name ] = $value;
        }

        if(strpos($file, '.twig') === false) {
            $file = "{$file}.twig";
        }

        $twig = Outer_Twig::get("{$_SERVER['DOCUMENT_ROOT']}/includes/module/".get_class( $this )."/template");
        return $twig->loadTemplate($file)->render($vars);

        //return new Template( "modules/". get_class( $this ). "/{$file}", $vars );
    }
    
    /**
    *   Получение ответа на HTML-запрос.
    *   Может и чаще всего должен быть переопределен в потомках для реализации непростой функциональности.
    */
    function handleHtmlRequest( $uri ) {
        return $this->getTemplate( 'index' );
    }

    /**
    *   Получение ответа на HTML-запрос.
    *   Может быть переопределен в потомках для реализации непростой функциональности, недоступной через запросы к моделям.
    */
    function handleAjaxRequest( $uri ) {
        return json_encode( array( 'success' => true, 'message' => get_class( $this ).' приветствует Вас и желает Вам приятного дня.' ) );
    }
}

?>