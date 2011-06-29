<?
/**
Класс условия выборки
*/

class StormQC extends StormQCOp
{
    public $optree; #TODO Заменить на protected!

    function __construct( $params )
    {
        // Проверим параметры, это должен быть массив, integer или объект StormModel
        if( ! is_array( $params ) )
        {
            if( ! ( is_numeric( $params ) || $params instanceof StormModel ) )
                throw new StormException( 'Must use array, integer or StormModel instance to create query parameter' );
            $params = array( 'pk' => is_object( $params ) ? $params->meta->getPkValue() : (int)$params );
        }
    
        foreach( $params as $field => $value )
        {
            $this->optree = $this->optree ?
            new StormQCAndOp( $this->optree, new StormQCCheckOp( $field, $value ) ) :
            new StormQCCheckOp( $field, $value );
        }
    }

    function getStormQueryParts( $model )
    {
        return $this->optree->getStormQueryParts( $model );
    }
}

?>
