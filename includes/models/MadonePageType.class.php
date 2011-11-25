<?
/**
    Тип страницы сайта
*/

class MadonePageType extends Storm_Model {
    private $instance=null;  // Инициализированный объект класса app_classname

    static function definition() {
        return array (
            'title'			=> new Storm_Db_Field_Char( array( 'localized'=> false, 'maxlength' => 100 ) ),
            'app_classname'	=> new Storm_Db_Field_Char( array( 'localized'=> false, 'maxlength' => 100, 'default' => 'MadoneMadone_Application', 'index' => true ) ),
            'settings'		=> new Storm_Db_Field_Text( array( 'localized' => false ) ),
            'enabled'		=> new Storm_Db_Field_Bool( array( 'default' => true ) ),
            'has_text'		=> new Storm_Db_Field_Bool( array( 'default' => true ) ),
            'has_meta'		=> new Storm_Db_Field_Bool( array( 'default' => true ) ),
            'position'		=> new Storm_Db_Field_Integer(),
            'has_subpages'	=> new Storm_Db_Field_Bool( array( 'default' => false ) ),
            'priority'		=> new Storm_Db_Field_Integer( array( 'index' => true ) ),
        );
    }
    
    function afterSave( $new ) {
        if( $new ) {
            $changes = false;
        
            if( ! $this->position ) {
                $last = $this->getQuerySet()->filter( array( 'id__ne' => $this->id ) )->orderDesc( 'position' )->first();
                $this->position = $last ? $last->position + 1 : 1;
                $changes = true;
            }
            
            if( ! $this->priority ) {
                $last = $this->getQuerySet()->filter( array( 'id__ne' => $this->id ) )->orderDesc( 'priority' )->first();
                $this->priority = $last ? $last->priority + 1 : 1;
                $changes = true;
            }
            
            if( $changes ) {
                $this->save();
            }
        }
    }
    
    /**
     * Получение объекта приложения madone
     */
    public function getApplicationInstance() {
        if( ! $this->instance ) {
        	$this->instance = new $this->app_classname();
        }
        
        return $this->instance; 
    }
}

?>
