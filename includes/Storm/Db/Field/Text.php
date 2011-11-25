<?
/**
    Поле типа text
*/
class Storm_Db_Field_Text extends Storm_Db_Field
{
    protected $maxlength; // Максимальная длина поля, необязательный параметр
	protected $localized = true;
    /**
        Установка значения поля
    */
    function setValue( $string, $language = null )
    {
        // Проверяем значение, нехорошее значение вызывает exception
        if( $this->maxlength && mb_strlen( $string, 'utf-8' ) > $this->maxlength )
        {
            throw new Storm_Exception_Validation( "Value '$string' exceeds maximal length of $this->maxlength characters", $this );
        }

        return parent::setValue( $string, $language );
    }
}

?>
