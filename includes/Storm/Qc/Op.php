<?

/**
    ������� �������� StormQueryCheck
*/
class Storm_Qc_Op
{
    /**
        ��������� ��������� �������� ���������� ����� �����
    */
    function __get( $name )
    {
        if( property_exists( $this, $name ) ) return $this->$name;
    }

    /**
        ��������� �������� ��� like-��������
        $value - ���������, ������� ����� ������
        $leading - boolean, ��������� ������ ���� ������� ������� ������
        $trailing - boolean, ��������� ������ ���� ���������� ������� ������
    */
    protected function getLikeValue( $value, $leading = true, $trailing = true )
    {
        // ��� ������ ���������� ������ � $value
        $value = str_replace( '_', '\_', str_replace( '%', '\%', $value ) );

        // � ������ ������� ��������� �������
        return ( $leading ? '' : '%' ) . $value . ( $trailing ? '' : '%' );
    }
}

?>
