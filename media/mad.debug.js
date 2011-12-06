/**
	Удобный дамп любых объектов в alert, хорош для отладки.
	Использование:
		var something = [ 42, { a: 'string', b: new String( 'string object' ), c: null }, function() { return 'hi!'; } ];
		alert( something );
	В продакшн версиях не подключать.
	Взято с http://dklab.ru/chicken/nablas/39.html, доработано рашпилем.
*/
Array.prototype.toString = Object.prototype.toString = function() {
	var contents = [];

	if( this instanceof jQuery ) {
		return 'jQuery( "' + this.selector + '" )';
	}
	
	for( var k in this ) {
		if( contents.length ) {
			contents[ contents.length - 1 ] += ",";
		}

		var v = this[k];
		var vs = '';	// строковое значение текущего элемента объекта
		
		if( v instanceof String ) {
			vs = '"' + v + '"';
		} else if( typeof v === 'string' ) {
			vs = "'" + v + "'";
		} else {
			try {
				vs = v === null ? 'null' : v.toString(); // а тут такой неявный шаг в рекурию через toString
			}
			catch(e) {
			}
			
		}
		
		if( this instanceof Array ) {
			contents[ contents.length ] = vs;
		} else {
			contents[ contents.length ] = k + ": " + vs;
		}
	}
		
	// Нельзя делать replace(), ибо он вызывает бесконечную рекурсию через toString в опере, поэтому join/split/join
	contents = "  " + contents.join("\n").split("\n").join("\n  ");
	
	if( this instanceof Array ) {
		contents = "[\n" + contents + "\n]";
	} else if( this instanceof Object ) {
		contents = "{\n" + contents + "\n}";
	}
	
	return contents;
};