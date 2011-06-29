<?

/**
    Сообщение административного интерфейса
*/

class MadoneModuleMessage
{
    public $critical = false;  // Тип сообщения, если true — ошибка, иначе просто info.
    public $text = '';
    
    function __construct( $text, $critical = false )
    {
        $this->text = $text;
        $this->critical = $critical;
    }
}

?>