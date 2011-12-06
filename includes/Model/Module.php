<?
/**
 * Model_Module class.
 * Модуль административного интерфейса Madone
 * @extends Storm_Model
 */
class Model_Module extends Storm_Model {
    private $instance=null;  // Инициализированный объект модуля Madone, соответствующего classname

    static function definition() {
        return array (
            'title'       => new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 100, 'default' => 'Новый модуль' ) ),
            'name'        => new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 100, 'default' => 'new-module', 'index' => true ) ),
            'enabled'     => new Storm_Db_Field_Bool( array( 'default' => true ) ),
        );
    }
    
    function beforeSave() {
    	// Если в базе уже есть объект с таким name
   		if($this->getQuerySet()->filter( array('id__ne' => $this->id, 'name' => $this->name ) )->first()) {
   			throw new Exception("Модуль с именем {$this->name} уже существует! Придумайте другое имя!");
   		}

    	// Если в базе уже есть объект с таким classname
   		if( $this->getQuerySet()->filter( array('id__ne' => $this->id, 'classname' => $this->classname ) )->first() ) {
			throw new Exception("1. Класс с именем {$this->classname} уже существует! Придумайте другое имя!");
   		}

		// TODO: Эта штука должна работать в зависимости от контекста
    	// Если уже есть класс с именем classname
/*
		if(! MadoneModules()->filter( array('id' => $this->id ) )->first() && 
			 @class_exists( $this->classname, true) ) {
			 
    		throw new Exception("2. Класс с именем {$this->classname} уже существует! Придумайте другое имя!");
    	}   		
*/

   		
   		// Если в базе уже существует такой обеъкт и мы хотим его изменить
		if($this->id) {
			$obj = $this->getQuerySet()->get( $this->id );
	   		if($obj) {
	   			// Если мы собираемся переименовать класс
	   			if($obj->classname != $this->classname) {
	   				$path = getcwd();
	   				if( file_exists("{$path}/includes/modules/{$this->classname}.class.php") || 
	   					file_exists("{$path}/templates/modules/{$this->classname}/") ) {
	   					
	   					throw new Exception("Файлы соответствующие такому имени класса уже существуют! Удалите их, либо придумайте другое имя класса!");
	   				}
					
					// Переименуем файл с классом
					rename("{$path}/includes/modules/{$obj->classname}.class.php", "{$path}/includes/modules/{$this->classname}.class.php");
					// Исправим имя класса внутри файла
					$file_content = file_get_contents("{$path}/includes/modules/{$this->classname}.class.php");
					$file_content = preg_replace("/class $obj->classname /", "class $this->classname ", $file_content);
					file_put_contents("{$path}/includes/modules/{$this->classname}.class.php", $file_content);
					
					// Переименуем папку с шаблонами
					rename("{$path}/templates/modules/{$obj->classname}/", "{$path}/templates/modules/{$this->classname}/");
	   			}
	   		}		
		}
    }
    
    function afterSave( $new ) {
        if( $new && ! $this->position ) {
        	// Позиция
            $last = $this->getQuerySet()->filter( array( 'id__ne' => $this->id ) )->orderDesc( 'position' )->first();            
            $this->position = $last ? $last->position + 1 : 1;
			
            $this->hiddenSave();
        }
    }
        
    /**
     * getInstance function.
     * Получение объекта модуля для административного интерфейса
     * @access private
     * @return Model_Module
     */
    private function getInstance() {
        if( ! $this->instance ) $this->instance = new $this->classname( $this->name );
        return $this->instance;
    }
    
    /**
        Получение контента модуля
    */
    function handleHtmlRequest( $uri ) {
        return $this->getInstance()->handleHtmlRequest( $uri );
    }

    /**
        Получение ajax контента модуля
    */
    function handleAjaxRequest( $uri ) {
        return $this->getInstance()->handleAjaxRequest( $uri );
    }

    /**
        Получение название модуля
    */
    function getTitle() {
        return $this->getInstance()->getTitle();
    }
}

?>
