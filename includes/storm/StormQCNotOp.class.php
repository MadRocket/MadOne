<?

/**
    �������� � ���������
*/

class StormQCNotOp extends StormQCOp
{
    protected $op;

    /**
        �����������
    */
    function __construct( StormQCOp $op )
    {
        $this->op = $op;
    }

    /**
        ��������� SQL
    */
    function getStormQueryParts( $model )
    {
        // ������� SQL-��������� ���������, ������� �� ��������
        $r = $this->op->getStormQueryParts( $model );

        // � ����������, �������� ��� ������� �������
        $r['where'] = "( NOT {$r['where']} )";

        return $r;
    }
}

?>
