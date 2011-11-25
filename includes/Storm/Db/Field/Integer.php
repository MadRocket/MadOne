<?
/**
    ���� ���� integer
*/
class Storm_Db_Field_Integer extends Storm_Db_Field
{
    /**
        ��������� �������� ����
    */
    function setValue( $string, $language = null )
    {
        if( ! is_null( $string ) )
        {
        
            // ��������� ��������, ��������� �������� �������� exception
            if( ! is_numeric( $string ) )
            {
                throw new Storm_Exception_Validation( "'$string' is not valid integer value", $this );
            }

            return parent::setValue( (int)$string, $language );
        }
        else
        {
            return parent::setValue( $string, $language );
        }
    }
}

?>
