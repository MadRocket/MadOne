<?
/**
    ���� ����������� ����
*/
class StormBoolDbField extends StormDbField
{
    /**
        ��������� �������� ����
    */
    function setValue( $string, $language = null )
    {
        return parent::setValue( $string ? 1 : 0, $language );
    }
    
    function setValueFromDatabase( $value, $language = null ) {
    	if( ! is_null( $value ) ) {
    		$value = $value ? 1 : 0;
    	}
		return parent::setValueFromDatabase( $value, $language );
    }
    
    /**
        ��������� ��������, ������������������ ��� �������
    */
    static public function getCheckOpValue( $value )
    {
        return $value ? '1' : '0';
    }

}

?>
