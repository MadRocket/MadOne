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
     * @throws Exception
     * @param $name - имя, под которым модуль будет работать
     */
    function __construct( $name ) {

        // Проверим наличие имени
        if( ! $name ) {
            throw new Exception( "MadoneModule cannot be constructed without a name" );
        }

        // Получим всякие uri от запроса CMS
        $request = new MadoneCmsRequest();
        
        $this->cmsUri   = $request->getCmsUri();
        $this->uri      = "{$this->cmsUri}/{$name}";
        $this->ajaxUri  = "{$this->cmsUri}/ajax/{$name}";
        $this->uriPath  = $this->cmsUri . $request->getObjectName() == $name ? "/{$name}".( $request->getUri() != '/' ? $request->getUri() : '' ) : "/{$name}";
    }
    
    /**
     * @param $file
     * @param array $vars
     * @return string
     */
    function getTemplate( $file, $vars = array() ) {

        foreach( get_object_vars( $this ) as $name => $value ) {
            $vars[ $name ] = $value;
        }

        if(strpos($file, '.twig') === false) {
            $file = "{$file}.twig";
        }

        $twig = Outer_Twig::get(
            array(
                "{$_SERVER['DOCUMENT_ROOT']}/includes/module/".get_class( $this )."/template",
                "{$_SERVER['DOCUMENT_ROOT']}/includes/template/admin"
            )
        )
        ;
        
        return $twig->loadTemplate($file)->render($vars);
    }

    /**
     * Получение ответа на HTML-запрос.
     * Может и чаще всего должен быть переопределен в потомках для реализации непростой функциональности.
     * @param $uri
     * @return string
     */
    function handleHtmlRequest( $uri ) {
        return $this->getTemplate( 'index.twig' );
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