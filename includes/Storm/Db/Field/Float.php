<?
/**
    ���� ���� float
*/
class Storm_Db_Field_Float extends Storm_Db_Field
{
    /**
     * setValue function.
     * ��������� �������� ����
     * @access public
     * @param mixed $string
     * @return void
     */
    function setValue( $string, $language = null )
    {
        if( ! is_null( $string ) )
        {
        
            // ��������� ��������, ��������� �������� �������� exception
            if( ! is_numeric( $string ) )
            {
                throw new Storm_Exception_Validation( "'$string' is not valid float value", $this );
            }

            return parent::setValue( (float)$string, $language );
        }
        else
        {
            return parent::setValue( $string, $language );
        }
    }
}

?>
