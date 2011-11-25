<?
/*
    Класс выборки значений по внешнему ключу объекта связанной модели
*/

class Storm_Queryset_Fk extends Storm_Queryset
{
    # Ссылка на объект модели, с которым связываем свои объекты
    protected $key_instance;
    protected $field_name;

    function __construct( $model, $field_name, Storm_Model $key_instance )
    {
        parent::__construct( $model );

        $this->key_instance = $key_instance;
        $this->field_name = $field_name;

        $this->qc = new Storm_Qc( array( "{$this->field_name}__exact" => $this->key_instance ) );
    }
    
    /**
        Создание объекта и привязка его к связанному объекту
    */
    function create( array $params )
    {
        # Просто добавим в $params наш объект и создадим новый экземпляр связанного объекта
        $params[ $this->field_name ] = $this->key_instance;
        return parent::create( $params );
    }

    /**
        Привязка переданных объектов к связанному объекту
    */
    function add()
    {
        foreach( func_get_args() as $obj )
        {
            if( ! $obj instanceof $this->model )
            {
                throw new Storm_Exception( "Can add '{$this->model}' instances only" );
            }
            
            # Установим текущий связанный объект и сохраним 
            $obj->{ $this->field_name } = $this->key_instance;
            $obj->save();
        }
    }

    /**
        Отвязка переданных объектов от связанного объекта
    */
    function remove()
    {
        foreach( func_get_args() as $obj )
        {
            if( ! $obj instanceof $this->model )
            {
                throw new Storm_Exception( "Can remove '{$this->model}' instances only" );
            }

            try 
            {
                $obj->{ $this->field_name } = NULL;
            }
            catch( FieldNotNullException $e )
            {
                throw new Storm_Exception( "Cannot diassociate {$this->model} objects due to not null constraint" );
            }

            $obj->save();
        }
    }

    /**
        Отвязка всех объектов, связанных с текущим
    */
    function clear()
    {
        foreach( $this->all() as $obj )
        {
            try 
            {
                $obj->{ $this->field_name } = NULL;
            }
            catch( Storm_Exception_Validation $e )
            {
                throw new Storm_Exception( "Cannot diassociate {$this->model} objects due to not null constraint" );
            }

            $obj->save();
        }
    }
}

?>
