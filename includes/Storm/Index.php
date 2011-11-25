<?
/**
    Индекс модели Storm.
*/

class Storm_Index
{
    public $model = null; // Имя модеи
    public $unique = false; // Уникальный?
    public $primary = false; // Primary Key?
    public $fulltext = false; // Полнотекстовый?
    public $fields = array(); // массив имен полей индекса
    
    /**
        Конструктор
        $model - имя модели
        $fields - массив имен полей
    */
    function __construct( $model, array $fields = array() )
    {
        $this->model = $model;
        foreach( $fields as $f ) $this->addField( $f );
    }
    
    /**
        Добавление имени индексируемого поля
    */
    function addField( $name )
    {
        if( ! in_array( $name, $this->fields ) )
        {
            $this->fields[] = $name;
            return true;
        }
        
        return false;
    }
    
    /**
        Получение имени индекса.
    */
    function getName()
    {
    	if( $this->primary ) {
    		return 'PRIMARY';
    	}
    	$name = join( '__', $this->fields ) . '__';
    	if( $this->unique ) {
    		$name .= 'uq';
    	}
    	if( $this->fulltext ) {
    		$name .= 'ft';
    	}
    	$name .= 'idx';
        return strlen( $name ) < 50 ? $name : md5( $name );
    }
}

?>