<?
/*
    Класс выборки значений по внешнему ключу объекта связанной модели
*/

class StormFkQuerySet extends StormQuerySet
{
    # Ссылка на объект модели, с которым связываем свои объекты
    protected $key_instance;
    protected $field_name;

    function __construct( $model, $field_name, StormModel $key_instance )
    {
        parent::__construct( $model );

        $this->key_instance = $key_instance;
        $this->field_name = $field_name;

        $this->qc = new StormQC( array( "{$this->field_name}__exact" => $this->key_instance ) );
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
                throw new StormException( "Can add '{$this->model}' instances only" );
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
                throw new StormException( "Can remove '{$this->model}' instances only" );
            }

            try 
            {
                $obj->{ $this->field_name } = NULL;
            }
            catch( FieldNotNullException $e )
            {
                throw new StormException( "Cannot diassociate {$this->model} objects due to not null constraint" );
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
            catch( StormValidationException $e )
            {
                throw new StormException( "Cannot diassociate {$this->model} objects due to not null constraint" );
            }

            $obj->save();
        }
    }
}

?>
