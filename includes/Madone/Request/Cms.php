<?
/**
    Запрос к CMS
    Разбирает URI на части, берет на себя выделение AJAX-запросов, активного модуля и формирование URI для него.
*/
    
class Madone_Request_Cms {

    const MODEL         = 1;    // Запрос к модели
    const MODULE_HTML   = 2;    // HTML-запрос к модулю cms 
    const MODULE_AJAX   = 3;    // AJAX-запрос к модулю cms
    const LANG_SWITCH	= 4;	// Запрос переключения языка

    protected $type = null;
    protected $objectName = null;
    protected $uri = null;
    protected $cmsUri = null;

    /**
        Конструктор
        $uri - uri запроса, при отсутствии получает его самостоятельно.
    */
    function __construct( $uri =  null )
    {
        // Проверим и инициализируем uri
        if( $uri == null ) {
            $uri = Madone_Utilites::getUriPath();
        }
     
        // Получим путь в виде массива имен
        $names = Madone_Utilites::getUriPathNames( $uri );
        
        // Отбросим путь в административный раздел и сохраним его во внутренней переменной
        // Расчет в том, что объект создается в скрипте административного раздела, который расположен в его корне :D
        // FIXME: грубый хак
        $script_names = Madone_Utilites::getUriPathNames( '/admin' . Madone_Utilites::getUriPath( $_SERVER['SCRIPT_NAME'] ) );
        
        for( $this->cmsUri = array(); $script_names[0] == $names[0]; ) {
            $this->cmsUri[] = array_shift( $names );
            array_shift( $script_names );
            
            if(! count($script_names) || ! count($names) ) {
            	break;
            }
        }
        $this->cmsUri = '/'.join( '/', $this->cmsUri );
        
        // Если следующее имя начинается с заглавной буквы — это обращение к модели
        if( array_key_exists(0, $names) && preg_match( '/^[A-Z]/', $names[0] ) && class_exists( $names[0] ) && is_subclass_of( $names[0], 'Storm_Model' ) ) {
            $this->type = self::MODEL;
        }
        // переключение языка
        elseif( array_key_exists(0, $names) && $names[0] == 'switchlanguage' ) {
            $this->type = self::LANG_SWITCH;
            array_shift( $names );
        }
        // Все остальные обращения - к модулю, ajax или html
        elseif( array_key_exists(0, $names) && $names[0] == 'ajax' ) {
            $this->type = self::MODULE_AJAX;
            array_shift( $names );
        }
        elseif( array_key_exists(0, $names) && $names[0] ) {
            $this->type = self::MODULE_HTML;
        }
        
        // Если в пути есть еще что-то, то это имя объекта
        if( $names ) {
            $this->objectName = array_shift( $names );
        }

        // Все оставшееся — путь внутри модуля
        $this->uri = '/'.join( '/', $names );
    }
    
    /**
    *   Получение защищенных полей для чтения
    */
    function __get( $name )
    {
        error_log('Deprecated');
        return property_exists( $this, $name ) ? $this->$name : null;
    }

    public function getCmsUri()
    {
        return $this->cmsUri;
    }

    public function getObjectName()
    {
        return $this->objectName;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUri()
    {
        return $this->uri;
    }
}

?>