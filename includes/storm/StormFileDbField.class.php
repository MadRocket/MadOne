<?
/**
 * Поле типа file
 * В качестве значения может принимать следующие штуки:
 *	1.	Внутренний формат, хранящийся в БД — {"name": kawaii.ext, "size": 200}
 *	2.	Массив с полями tmp_name, name, error, так же как в $_FILES.
 *		Такой режим позволяет сохранять изображения напрямую из input type=file.
 *	4.	Путь в файловой системе относительно DOCUMENT_ROOT вида /uploaded/files/05.ext.
 *		Система попробует скопировать этот файл себе, исходный оставив в неприкосновенности.
 *		Файл не должен быть 
 */
class StormFileDbField extends StormDbField {
	protected $valueClassname = 'StormFileDbFieldValue'; // Имя класса значений
}

?>