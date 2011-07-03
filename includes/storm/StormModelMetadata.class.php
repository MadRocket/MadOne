<?
/**
    Метаданные модели Storm.
    Хранит имя, список полей и их типы.
    Предоставляет некоторые функции для анализа метаданных.
*/
    
class StormModelMetadata
{
    const pkname = 'id'; // Имя первичного ключа по умолчанию

    protected $name;    // имя модели
    protected $fields;  // массив полей модели, с реальными объектами и их значениями
    protected $fkfields; // массив полей-внешних ключей модели.
    protected $pkname;  // имя первичного ключа модели
    protected $pk;      // ссылка на поле-первичный ключ
    
    /**
        Конструктор.
        Принимает имя модели и массив полей, сгенеренный функцией definition() класса модели.
    */
    function __construct( $name, array $fields )
    {
        // Наполняем мета-информацию
        $this->name = $name;
        $this->fields = $fields;
        
        //  Найдем ключевое поле модели
        foreach( $this->fields as $name => $instance )
        {
            $instance->name = $name;
        
            // Сохраним ссылку на ключевое поле
            if( $instance instanceof StormAutoDbField )
            {
                if( $this->pkname )
                {
                    throw new StormException( "Cannot use more than one StormAutoDbField in {$this->name} model definition" );
                }
                $this->pkname = $name;
                $this->pk = $instance;
            }

            // Сохраняем нешние ключи            
            if( $instance instanceof StormFkDbField )
            {
                $this->fkfields[ $name ] = $instance;
            }
        }
        
        // Посмотрим, нашлось ли ключевое поле, если нет - создадим его сами
        if( ! $this->pk )
        {
            // Проверим, не занято ли имя первичного ключа
            if( array_key_exists( StormModelMetadata::pkname, $this->fields ) )
            {
                throw new StormException( "Could not create a primary key field for {$this->name} model as the default PK name '". StormModelMetadata::pkname ."' is already in use" );
            }

            $this->pkname = StormModelMetadata::pkname;
            $this->pk = new StormAutoDbField();
            $this->pk->name = $this->pkname;
            $this->fields = array_merge( array( $this->pkname => $this->pk ), $this->fields );
        }
    }
    
    /**
        Доступ на чтение к полям name, pk и pkname
    */
    function __get( $name )
    {
        if( in_array( $name, array( 'name', 'pkname', 'pk' ) ) ) return $this->$name;
        
        throw new StormException( "Inaccessible '{$name}' property of StormModelMetadata" );
    }

    /**
        Получение полей модели.
    */    
    function getFields()
    {
        return $this->fields;
    }
    
    /**
        Проверка существования поля
    */
    function fieldExists( $name )
    {
        return array_key_exists( $name, $this->fields );
    }
    
    /**
     * Получение ссылки на поле
     * @throws StormException
     * @param $name
     * @return StormDbField
     */
    function getField( $name )
    {
    	if( ! array_key_exists( $name, $this->fields ) ) {
    		throw new StormException( "There is no '{$name}' field in {$this->name} model" );
		}
        return $this->fields[ $name ];
    }
    
    /**
        Получение значения первичного ключа
    */
    function getPkValue()
    {
        return $this->pk->getValue();
    }
    
    /**
        Eстановка значения первичного ключа
    */
    function setPkValue( $value )
    {
        return $this->pk->setValue( $value );
    }

    public function setFkfields($fkfields)
    {
        $this->fkfields = $fkfields;
    }

    public function getFkfields()
    {
        return $this->fkfields;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPkname()
    {
        return $this->pkname;
    }
}
    
?>
