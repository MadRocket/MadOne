<?
/**
 * Поле типа varchar
 */
class StormCharDbField extends StormDbField
{
    protected $maxlength = 255; // Максимальная длина поля
	protected $localized = true;

    /**
     * Конструктор.
     * Проверяет параметры, касающиеся настроек поля
     */
    function __construct( array $params = array() )
    {
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
            throw new StormValidationException( "Value '$string' exceeds maximal length of $this->maxlength characters", $this );
        }

        return parent::setValue( $string, $language );
    }
}

?>
