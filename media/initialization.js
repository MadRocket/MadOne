/**
*	Здесь расположены инициализации JS-штук системы управления.
*	Этот скрипт должен быть подключен самым последним.
*/
$( function() {
	// Устанавливаем uri Storm-процессора
	Storm.processorUri = Madone.uri;
	
	// Запрещаем перетаскивание за кнопки, что бы это не значило :D
	$( '.control img, .actions img' ).mousedown( function ( e ) {
		e.stopPropagation();
	} );
	
	// Подключение html-редакторов
	Madone.enableRichTextareas();
	
	// Подключение календариков
	Madone.enableDatepickers();
} );