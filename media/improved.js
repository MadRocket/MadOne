/*
Библиотека улучшения javascript, написанная под вдохновением от javascript.crockford.com, откуда взята часть кода.
http://javascript.crockford.com/remedial.html
*/

/**
Создание клона объекта на основе переданного прототипа.
Забудьте о классах, создавайте объекты, и наследуйте их!
Читать тут: http://javascript.crockford.com/prototypal.html
*/
if( typeof Object.create !== 'function' ) {
	Object.create = function ( o ) {
		function F() {}
		F.prototype = o;
		return new F();
    };
}

/**
	Оператор typeof работает неправильно — для массивов и null возвращается тип 'object'.
	Функция Object.typeOf призвана исправить эту ситуацию.
*/
if( typeof Object.typeOf !== 'function' ) {
	Object.typeOf = function ( value ) {
		var s = typeof value;
		if( s === 'object' ) {
			if( value ) {
				if( value instanceof Array ) {
					s = 'array';
				}
			} else {
				s = 'null';
			}
		}
		return s;
	};
}

/**
*	Полезный объект, который можно использовать в качестве прототипа.
*	Имеет один единственный метод extend в базовой поставке — копирование в себя самого всех переданных аргументами объектов.
*	Копируются только прямые члены объекта, полученные от прототипов — нет.
*/
if( typeof Object.Extendable !== 'object' ) {
	Object.Extendable = {};
	Object.Extendable.extend = function() {
		for( var i = 0; i < arguments.length; i++ ) {
			for( var member in arguments[i] ) {
				if( arguments[i].hasOwnProperty( member ) ) {
					this[ member ] = arguments[ i ][ member ];
				}
			}
			return this;
		}
	};
}


/**
	Создание делегата метода объекта.
	Фозвращает функцию, при вызове вызыващую переданный метод в контексте переданного объекта с реальными аргументами.
	Возвращает результат выполнения метода.
*/
if( typeof Function.delegate !== 'function' ) {
	Function.delegate = function ( object, method ) {
		return function () {
			return method.apply( object, arguments );
		};
	};
}

/**
	Создание коллбэка функции с «замороженными» аргументами.
	Фозвращает функцию, при вызове вызывающую переданную функцию с переданными аргументами.
	Возвращает результат выполнения функции.
*/
if( typeof Function.callback !== 'function' ) {
	Function.callback = function ( fnct ) {
		// «Замораживаем» аргументы и саму функцию
		var args = [];
		for( var i = 0; i < arguments.length; i++ ) {
			args[i] = arguments[i];
		}
		return function () {
			return args[0].apply( null, args.slice( 1 ) );
		};
	};
};

/**
*	Заполнение строки значениями переданного объекта по шаблону, аналог printf.
*	data - объект с данными 
*	Возвращает строку с замененными данными.
*	Пример 'Самое время отправиться {where}!'.supplant( { where: 'погулять' } );
*/
String.prototype.supplant = function ( data ) {
	return this.replace( /{([^{}]+)}/g, function( a, b ) {
		var r = data[b];
		return r !== undefined ? r : a;
	} );
};
