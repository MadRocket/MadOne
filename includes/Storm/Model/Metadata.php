<?
/**
    Метаданные модели Storm.
    Хранит имя, список полей и их типы.
    Предоставляет некоторые функции для анализа метаданных.
*/
    
class Storm_Model_Metadata
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
            if( $instance instanceof Storm_Db_Field_Auto )
            {
                if( $this->pkname )
                {
                    throw new Storm_Exception( "Cannot use more than one Storm_Db_Field_Auto in {$this->name} model definition" );
                }
                $this->pkname = $name;
                $this->pk = $instance;
            }

            // Сохраняем нешние ключи            
            if( $instance instanceof Storm_Db_Field_Fk )
            {
                $this->fkfields[ $name ] = $instance;
            }
        }
        
        // Посмотрим, нашлось ли ключевое поле, если нет - создадим его сами
        if( ! $this->pk )
        {
            // Проверим, не занято ли имя первичного ключа
            if( array_key_exists( Storm_Model_Metadata::pkname, $this->fields ) )
            {
                throw new Storm_Exception( "Could not create a primary key field for {$this->name} model as the default PK name '". Storm_Model_Metadata::pkname ."' is already in use" );
            }

            $this->pkname = Storm_Model_Metadata::pkname;
            $this->pk = new Storm_Db_Field_Auto();
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
        
        throw new Storm_Exception( "Inaccessible '{$name}' property of Storm_Model_Metadata" );
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
     * @throws Storm_Exception
     * @param $name
     * @return Storm_Db_Field
     */
    function getField( $name )
    {
    	if( ! array_key_exists( $name, $this->fields ) ) {
    		throw new Storm_Exception( "There is no '{$name}' field in {$this->name} model" );
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
