<?
/**
    Тип страницы сайта
*/

class MadonePageType extends StormModel {
    private $instance=null;  // Инициализированный объект класса app_classname

    static function definition() {
        return array (
            'title'			=> new StormCharDbField( array( 'localized'=> false, 'maxlength' => 100 ) ),
            'app_classname'	=> new StormCharDbField( array( 'localized'=> false, 'maxlength' => 100, 'default' => 'MadoneAbstractApplication', 'index' => true ) ),
            'settings'		=> new StormTextDbField( array( 'localized' => false ) ),
            'enabled'		=> new StormBoolDbField( array( 'default' => true ) ),
            'has_text'		=> new StormBoolDbField( array( 'default' => true ) ),
            'has_meta'		=> new StormBoolDbField( array( 'default' => true ) ),
            'position'		=> new StormIntegerDbField(),
            'has_subpages'	=> new StormBoolDbField( array( 'default' => false ) ),
            'priority'		=> new StormIntegerDbField( array( 'index' => true ) ),
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
