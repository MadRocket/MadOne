<?

/**
    �������� � ���������
*/

class Storm_Qc_Op_Not extends Storm_Qc_Op
{
    protected $op;

    /**
        �����������
    */
    function __construct( Storm_Qc_Op $op )
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
