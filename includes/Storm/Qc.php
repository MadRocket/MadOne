<?
/**
����� ������� �������
*/

class Storm_Qc extends Storm_Qc_Op
{
    public $optree; #TODO �������� �� protected!

    function __construct( $params )
    {
        // �������� ���������, ��� ������ ���� ������, integer ��� ������ Storm_Model
        if( ! is_array( $params ) )
        {
            if( ! ( is_numeric( $params ) || $params instanceof Storm_Model ) )
                throw new Storm_Exception( 'Must use array, integer or Storm_Model instance to create query parameter' );
            $params = array( 'pk' => is_object( $params ) ? $params->meta->getPkValue() : (int)$params );
        }
    
        foreach( $params as $field => $value )
        {
            $this->optree = $this->optree ?
            new Storm_Qc_Op_Binary_And( $this->optree, new Storm_Qc_Op_Check( $field, $value ) ) :
            new Storm_Qc_Op_Check( $field, $value );
        }
    }

    function getStormQueryParts( $model )
    {
        return $this->optree->getStormQueryParts( $model );
    }
}

?>
