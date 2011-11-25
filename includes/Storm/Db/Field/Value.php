<?

/**
	Обычное значение базы данных Storm.
*/

class Storm_Db_Field_Value {
	protected $value;
	
	function __construct( array $params = array() ) {
	
	}
	
	// Нормальная работа в качестве поля таблицы
	function &get() {
		return $this->value;
	}
	
	// Нормальная работа в качестве поля таблицы
	function set( $value ) {
		$this->value = $value;
		return $this;
	}
	
	// Для сохранения значения в БД
	function getForDatabase() {
		return $this->value;
	}
	
	// Для инициализации значением из БД
	function setFromDatabase( $value ) {
		$this->value = $value;
		return $this;
	}

	// Получение значения в виде отдельного объекта упрощенного вида, например перевода в JSON
	function getSimplified() {
		return $this->value;
	}
}

?>