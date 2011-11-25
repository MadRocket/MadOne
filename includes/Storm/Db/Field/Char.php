<?
/**
 * Поле типа varchar
 */
class Storm_Db_Field_Char extends Storm_Db_Field
{
    protected $maxlength; // Максимальная длина поля
	protected $localized = true;

    /**
     * Конструктор.
     * Проверяет параметры, касающиеся настроек поля
     */
    function __construct( array $params )
    {
        // Проверим длину поля
        if( ! ( isset( $params['maxlength'] ) && $params['maxlength'] > 0 ) )
        {
            throw new Storm_Exception( "Must specify maxlength as positive integer" );
        }

        // Вызовем родительский конструктор, он прочитает все настройки
        parent::__construct( $params );
    }

	/**
	 * Установка значения поля
	 * @return
	 * @param object $string
	 */
    function setValue( $string, $language = null )
    {
        // Проверяем значение, нехорошее значение вызывает exception
        if( mb_strlen( $string, 'utf-8' ) > $this->maxlength )
        {
            throw new Storm_Exception_Validation( "Value '$string' exceeds maximal length of $this->maxlength characters", $this );
        }

        return parent::setValue( $string, $language );
    }
}

?>
