/*global $ window*/

/**
 * Собственно, Его Величество Глобальный Объект Storm. Пустой, методы будут позже :D
 */
var Storm = window.Storm = {
	processorUri: ''	// URI, по которому StormProcessor живет на сервере, должен быть установлен для правильной работы, или processor должен жить в корне сайта :)
};

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Параметры запроса Storm на выборку, некий аналог QuerySet-а. Только тут именно набор параметров, без методов их применения.
 * Замечателен тем, что если объект такого прототипа заполнить параметрами и сделать на его прототипе еще один объект,
 * то новый _скопирует_ себе параметры прототипа, и будет модифицировать уже свой набор параметров.
 */
Storm.Query = {};

/**
 * Добавление параметров выборки к запросу.
 * Принимает три вида аргументов. Каждый вызов use соответствует одной из функций серверного StormQuerySet.
 * Примеры:
 * На стороне браузера										На сервере					
 * query.use( 'order', 'pos' )								$QuerySet->order( 'position' )
 * query.use( 'filter', { pos__gt: 20, ready: true } )		$QuerySet->filter( array( 'pos_gt' => 20, 'ready' => true ) )
 * query.use( 'orderDesc,pos,level' )						$QuerySet->orderDesc( 'pos', 'level' )
 * Последний способ хорош, если аргументы простого запроса можно хранить в строке, и не нужно в динамике формировать.
 * Возвращает this во имя chain power.
 */
Storm.Query.use = function () {
	// Копируем свойство query, если его еще нет у нас
	if( ! this.hasOwnProperty( 'query' ) ) {
		this.query = this.query ? this.query.slice( 0 ) : [];
	}

	if( arguments.length ) {
		var args = [];	// Храним аргументы, разобранные в массив, первый — имя вызова, остальные — аргументы вызова
		if( arguments.length > 1 ) {
			for( var i = 0; i < arguments.length; i++ ) {
				args[i] = arguments[i];
			}
		} else {
			// один аргумент, посмотрим на счет перечисления в нем частей запроса через запятую типа order,position
			args = new String( arguments[0] ).split( ',' );
		}

		// Сохраняем
		var call = {};
		call[ args[0] ] = args.slice( 1 );
		this.query.push( call );
	}
	return this;
};

/**
 * Получение параметров запроса в виде, пригодном для Storm.
 * Возвращает json-кодированые параметры запроса.
 */
Storm.Query.get = function () {
    return { query: JSON.stringify(this.query) };
//	return { query: $.json.encode( this.query ) };
};


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Путь к объекту Storm - модели, объекту модели или полю объекта модели.
 * Предназначен для адресации в вызовах Storm.
 * На основе клонов Storm.Path строятся RESTfull URI запросов к штормпроцессору.
 */
Storm.Path = {
	model: null,	// Имя модели, например MadoneNews
	object: null,	// Идентификатор объекта, например 16
	field: null		// Имя поля объекта, например enabled
};

/**
 * Разбор строки — URI в собственные поля
 * path — исходная строка вида MadoneNews/16/enabled. Общий вид — имя_модели/идентификатор_объекта/имя_поля_объекта.
 *        имя поля или имя поля и идентификатор объекта могут отсутствовать.
 * Возвращает this
 */
Storm.Path.parse = function ( path ) {
	if( path ) {
		var parts = path.split( '/' );
		var names = [ 'model', 'object', 'field' ];
		for( var i = 0; i < names.length; i++ ) {
			this[ names[i] ] = parts[i] !== undefined ? parts[i] : null;
		}
	} else {
		this.model = this.object = this.field = null;
	}
	return this;
};

/**
* Получение полного URI пути. Возвращает путь до модели или объекта модели. Поле не учитывается.
 */
Storm.Path.getUri = function () {
	var uri = null;
	if( this.model ) {
		uri = Storm.processorUri + '/' + this.model;
		if( this.object ) {
			uri += '/' + this.object;
		}
	}
	return uri;
};

/**
* Получение пути модели
 */
Storm.Path.getModelUri = function () {
	return this.model ? Storm.processorUri + '/' + this.model : null;
};

/**
* Путь ведет к модели?
 */
Storm.Path.pointsAtModel = function () {
	return this.model ? true : false;
};
	
/**
* Путь ведет к объекту модели?
 */
Storm.Path.pointsAtObject = function () {
	return this.model && this.object ? true : false;
};
	
/**
* Путь ведет к полю объекта модели?
 */
Storm.Path.pointsAtField = function () {
	return this.model && this.object && this.field ? true : false;
};

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Все JSON-ответы от штормпроцессора представляют собой объекты с полями
 * success - boolean, флаг успешности;
 * data - данные в виде JSON-объекта, полученные от штормпроцессора в ответ на запрос;
 * message - сообщение, как правило, об ошибке.
 */

/**
 * Обработка ошибки Ajax соединения по умолчанию.
 * result - JSON-ответ от штормпроцессора.
 * Возвращает this.
 */
Storm.defaultOnFailure = function( result ) {
	alert( result.message );
	return this;
};

/**
 * Обработка результата Ajax-соединения.
 * result - JSON-ответ от штормпроцессора.
 * onSuccess - пользовательский обработчик успешного завершения.
 * onFailure - пользовательский обработчик неуспешного завершения. Может вернуть false для того, чтобы не запускать по умолчанию.
 * Возвращает, как заведено, ссылку на объект Storm, то есть this :D
 */
Storm.processAjaxResult = function ( result, onSuccess, onFailure ) {
	if( result.success ) {
		// В случае успеха вызываем пользовательский обработчик, если он есть. Если его нет — не делаем ничего.
		if( typeof onSuccess === 'function' ) {
			onSuccess.call( {}, result.data || {} );
		}
	} else {
		// С ошибками чуть сложнее — вызываем пользовательский обработчик, и следом — обработчик Storm по уполчанию, если только
		// пользовательский не вернул boolean значение false.
		if( typeof onFailure === 'function' ) {
			if( onFailure.call( {}, result.data || {} ) === false ) {
				return this;
			}
		}
		this.defaultOnFailure( result );
	}
	return this;
};

/**
 * Постройка Storm.Path на основе дерева DOM
 * Вытаскивает ближайшие переданному элементу атрибуты stormField, stormObject и stormModel.
 * Возвращает клон Storm.Path
 */
Storm.buildPath = function ( element ) {
	var path = Object.create( Storm.Path );
	var nesting = $( element ).parents().add( element );
	
	path.model	= nesting.filter( '[stormModel]:last'  ).attr( 'stormModel' );
	path.object	= nesting.filter( '[stormObject]:last' ).attr( 'stormObject' );
	path.field	= nesting.filter( '[stormField]:last'  ).attr( 'stormField' );
	
	return path;
};

/**
 * Получение пути из строки, если передан объект - возвращает его, не модифицируя ( это скорее всего Storm.Path ).
 * Возвращает клон Storm.Path или переданный объект.
 */
Storm.getPath =  function ( path ) {
	return Storm.Path.isPrototypeOf( path ) ? path : Object.create( Storm.Path ).parse( path );
};

/**
 * Все вызовы, так или иначе получающие данные из Storm, имеют последними двумя аргументами функции
 * onSuccess = function( data ) и onFailure = function()
 * Оба аргумента не являются обязательными.
 * В onFailure можно сделать return false для отмены запуска обработчика по умолчанию, иначе он запускается.
 * Для сохранения контекста обработчиков настоятельно рекомендуется использование использование Function.delegate().
 * В качестве контекста вызовов используется пустой объект.
 */

/**
 * Получение объекта, массива объектов, дерева объектов или еще чего угодно, что возвращает 
 * StormProcessor по запросам типа «дай этот объект этой модели», «отфильтруй эту модель», «сделай limit», «сделай tree»
 * и так далее. По сути — интерфейс ко _всем_ методам StormQuerySet. И результат — прям такой же, как получится на сервере,
 * только с учетом toArray и JSON-представления.
 * Аргументы
 * path - клон Storm.Path, путь к модели или объекту
 * * query - опциональный, параметры запроса к модели, объект (используется напрямуюв $.post) или клон Storm.Query.
 * onSuccess - function( data ), обработчик успешного получения данных, data - данные.
 * * onFailure - function(), обработчик неудачи.
 * Возвращает this.
 * Примеры:
 * 	Storm.retrieve( 'Model/777', function ( data ) {  alert( data.title ) } )
 * 	Storm.retrieve( 'Model', Object.create( Storm.Query ).use( 'filter', { name__contains: 'pattern' } ).use( 'order', 'name' ), function( data ) { alert( data.length ) } )
 */
Storm.retrieve = function () {
	var path, query, onSuccess, onFailure;
	var next = 0;

	path = Storm.getPath( arguments[ next++ ] );

	if( typeof arguments[ next ] === 'function' ) {
		query = undefined;
	} else {
		query = arguments[ next++ ];
		if( Storm.Query.isPrototypeOf( query ) ) {
			query = query.get();
		}
	}
	onSuccess	= arguments[ next++ ];
	onFailure	= arguments[ next++ ];
	
	if( query && path.pointsAtModel() ) {
		// Запрос к модели — фильтрация
		$.post( path.getModelUri() + '/retrieve/', query, function( r ) {
			Storm.processAjaxResult( r, onSuccess, onFailure );
		}, 'json' );
	} else if( path.pointsAtObject() ) {
		// Запрос к объекту — прямая выборка
		$.getJSON( path.getUri() + '/retrieve/', function ( r ) {
			Storm.processAjaxResult( r, onSuccess, onFailure );
		} );
	}
	else {
		this.defaultOnFailure( { success: false, message: 'Недопустимый для Storm.retrieve путь ' + arguments[0] + '.' } );
	}
	return this;
};


/**
 * Обновление одного или нескольких объектов
 * path - путь, может указывать на модель или на объект модели
 * data - данные, для обновления объекта — прямо набор его полей, 
 * 		для обновления модели — объект с ключами — идентификаторами обновляемых записей,
 * 		значения — объекты-наборы данных полей.
 * onSuccess
 * onFailure
 * Возвращает this.
 * Примеры:
 * Storm.update( 'Model/777', { enabled: 1 }, function( data ) {  alert( data.title ); } );
 * Storm.update( 'Model', { 1: { enabled: 1 }, 2: { enabled: 0 }, 3: { name: 'new value' } }, function() {  alert( 'Hooray!' ) } );
 */
Storm.update = function ( path, data, onSuccess, onFailure ) {
	path = this.getPath( path );

	if( path.pointsAtModel() || path.pointsAtObject() ) {
		$.post( path.getUri() + '/update/', path.pointsAtObject() ? { json_data: JSON.stringify( data ) } : { objects: JSON.stringify( data ) }, function ( r ) {
			Storm.processAjaxResult( r, onSuccess, onFailure );
		}, 'json' );
	} else {
		this.defaultOnFailure( { success: false, message: 'Недопустимый для Storm.update путь ' + arguments[0] + '.' } );
	}
	return this;
};

/**
 * Создание объекта из переданных данных
 * path - путь к модели
 * data - данные объекта
 * onSuccess
 * onFailure
 * Возвращает this
 */
Storm.create = function ( path, data, onSuccess, onFailure ) {
	path = this.getPath( path );
	if( path.pointsAtModel() ) {
		$.post( path.getModelUri() + '/create/', { json_data: JSON.stringify( data ) }, function ( r ) {
			Storm.processAjaxResult( r, onSuccess, onFailure );
		}, 'json' );
	} else {
		this.defaultOnFailure( { success: false, message: 'Недопустимый для Storm.create путь ' + arguments[0] + '.' } );
	}
	return this;
};

/**
 * Переупорядочивание ki-based объектов, удобно для использования с iNestedSortable.
 * path - модель
 * tree - дерево, задающее новый порядок узлов
 * onSuccess
 * onFailure
 * Возвращает this
 */
Storm.reorder = function ( path, tree, onSuccess, onFailure ) {
	path = this.getPath( path );
	if( path.pointsAtModel() ) {
		$.post( path.getModelUri() + '/reorder/', { objects: JSON.stringify( tree ) }, function ( r ) {
			Storm.processAjaxResult( r, onSuccess, onFailure );
		}, 'json' );
	} else {
		this.defaultOnFailure( { success: false, message: 'Недопустимый для Storm.reorder путь ' + arguments[0] + '.' } );
	}
	return this;
};

/**
 * Удаление объекта
 * path - путь к объекту
 * onSuccess
 * onFailure
 * Возвращает this.
 */
Storm.remove = function ( path, onSuccess, onFailure ) {
	path = this.getPath( path );
	if( path.pointsAtObject() ) {
		$.getJSON( path.getUri() + "/delete/", function ( r ) {
			Storm.processAjaxResult( r, onSuccess, onFailure );
		} );
	} else {
		this.defaultOnFailure( { success: false, message: 'Недопустимый для Storm.remove путь ' + arguments[0] + '.' } );
	}
	return this;
};

/**
 * Получение пустой структуры объекта, заполненной только значениями по умолчанию
 * path - модель
 * onSuccess
 * onFailure
 * Возвращает this.
 */
Storm.structure = function ( path, onSuccess, onFailure ) {
	path = this.getPath( path );
	if( path.pointsAtModel() ) {
		$.getJSON( path.getModelUri(), function ( r ) {
			Storm.processAjaxResult( r, onSuccess, onFailure );
		} );
	} else {
		this.defaultOnFailure( { success: false, message: 'Недопустимый для Storm.structure путь ' + arguments[0] + '.' } );
	}
	return this;
};


/**
 * Toggle булевого поля, адресуемого path
 * path - путь к полю объекта модели, типа MadoneNews/12/enabled
 * onSuccess
 * onFailure
 * Возвращает this.
 */
Storm.toggle = function ( path, onSuccess, onFailure ) {
	path = this.getPath( path );
	if( path.pointsAtField() ) {
		this.retrieve( path, function( data ) {
			var updateData = {};
			updateData[ path.field ] = data[ path.field ] ? 0 : 1;
			Storm.update( path, updateData, onSuccess, onFailure );
		}, onFailure );
	} else {
		this.defaultOnFailure( { success: false, message: 'Недопустимый для Storm.toggle путь ' + arguments[0] + '.' } );
	}
	return this;
};

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Процессор-обработчик форм редактирования шторм-моделей.
 * Берет на себя большую часть работы по получению/сохранению данных, отображению/скрытию собственно форм и прочее.
 * Пример:
*
*
*
 */

Storm.Form = Object.create( Object.Extendable ).extend( {
	urisBeingEdited: {}	// Хэш путей редактируемых в данный момент объектов. Важно, что это объект, следовательно все клоны Storm.Form будут пользоваться именно им.
} );

////// Обработчики, которые можно переопределить
/**
 * Форма запускается в работу, еще ничего не проверено и не подготовлено; самое начало процесса
 */
Storm.Form.onStart = function () {};

/**
 * Форма создана
 * form - jQuery ссылка на реальную копию формы, с которой будет идти работа
 */
Storm.Form.onCreate = function ( form ) {};

/**
 * Форма начала работу
 * form - jQuery ссылка на реальную копию формы
 */
Storm.Form.onLaunch = function ( form ) {};

/**
 * Загружены начальный данные формы
 * data - данные. объект можно модифицировать — добавлять поля, удалять или менять значения.
 */
Storm.Form.onLoadData = function ( data ) {};

/**
 * Прочитаны поля ввода формы
 * inputs - поля формы, объект, свойства - имена полей, значения - ссылки на HTML-элемент поля,
 * например для формы с единственным полем <input type="text" name="title"/>, объект inputs будет иметь вид
 * { title: _HTML_ELEMENT_ }, _HTML_ELEMENT_ - ссылка на объект DOM типа input, представляюший это поле ввода.
 * inputs можно модифицировать — добавлять, удалять элементы
 */
Storm.Form.onFetchInputs = function ( inputs ) {};

/**
 * Форма заполнена загруженными данными
 * form - jQuery, форма
 * data - данные, которыми заполнена форма, свойство - имя поля, значение - значение
 */
Storm.Form.onFill = function ( form, data ) {};

/**
 * Форма отображена
 * form - jQuery, форма
 */
Storm.Form.onShow = function ( form ) {};

/**
 * Получены данные, которые пользователь ввел в форму
 * form - jQuery, форма
 * data - данные, которыми заполнена форма, свойство - имя поля, значение - значение
 * объект data можно модифицировать.
 */
Storm.Form.onFetchData = function ( form, data ) {};

/**
 * Нажали кнопку сохранения, данные сохранены на сервер
 * form - jQuery, форма
 * data - данные, которые были отправлены на сервер для сохранения, свойство - имя поля, значение - значение
 * response - данные, полученные от сервера в ответ
 */
Storm.Form.onSubmit = function ( form, data, response ) {};

/**
 * Нажали кнопку отмены
 * form - jQuery, форма
 */
Storm.Form.onCancel = function ( form ) {};

/**
 * Объект заполнен данными, полученными после сохранения
 * item - jQuery, объект
 * response - данные, полученные от сервера при сохранении данных формы
 */
Storm.Form.onFillItem = function ( item, response ) {};

/**
 * Форма спрятана
 * form - jQuery, форма
 */
Storm.Form.onHide = function ( form ) {};

/**
 * Объект отображается вместо формы
 * item - jQuery, объект
 */
Storm.Form.onShowItem = function ( item ) {};

/**
 * Форма вот-вот будет уничножена, самое время удалить все с ней связанное
 * form - jQuery, форма
 */
Storm.Form.onDestroy = function ( form ) {};

////// Настраиваемые параметры
Storm.Form.single = true; // Редактирование только одного экземпляра объекта. Если форма с таким объектом уже открыта — форма просто не открывается.
Storm.Form.mode = null; // Режим работы, 'edit', 'create' или null, означающее автоопределение
Storm.Form.form = null;	// HTML-форма, jQuery
Storm.Form.formPlace = null; // Место в которое добавляется форма, jQuery
Storm.Form.object = null; // Storm.Path, путь к объекту, который будем редактировать или создавать на сервере
Storm.Form.item = null; // Элемент, вместо которого форма отображается при редактировании и который заполняется при закрытии формы, jQuery
Storm.Form.itemPlace = null; // Объект, в который добавляется item после заполнения в режиме create, jQuery
Storm.Form.submitButtonSelector = 'button.submit'; // jQuery селектор для кнопки отправки формы
Storm.Form.cancelButtonSelector = 'button.cancel'; // jQuery селектор для кнопки отмены редактирования
Storm.Form.formInputSelector = ':input[name]'; // jQuery селектор выборки полей редактирования данных формы

// Внутренние переменные формы, доступны внутри пользовательских обработчиков в том числе
Storm.Form.loadedData	= 	// Данные, загруженные с сервера методом loadData
Storm.Form.inputs		=	// Связка имен данных, полученных с сервера с полями ввода формы, заполняется методом fetchInputs
Storm.Form.data			=	// Данные, полученные методом fetchData
Storm.Form.response		=	// Данные, полученные от сервера в ответ на запрос сохранения данных
null;

/**
 * Проверка параметров.
 * Возвращает this или выбрасывает exception.
 */
Storm.Form.startCheck = function () {
	// Получим путь объекта, который редактируем
	this.object = Storm.getPath( this.object );
	
	// Если режим редактирования не указан — попробуем определить его самостоятельно
	// Принцип прост — если задан путь к модели, режим — create; если задан путь к объекту модели — edit.
	if( ! this.mode && this.object ) {
		if( this.object.pointsAtObject() ) {
			this.mode = 'edit';
		} else if( this.object.pointsAtModel() ) {
			this.mode = 'create';
		}
	} else if( ! ( this.mode === 'create' || this.mode === 'edit' ) ) {
		throw 'Параметр mode не указан, и определить его автоматически не удалось.';
	}
	
	if( ! this.form ) {
		throw 'Параметр form не указан.';
	}

	return this;
};

/**
 * Запуск формы. Этот метод следует вызвать, чтобы форма начала работать.
 * Возвращает true, если форма запущена или false, если объект формы уже редактируется такой же формой.
 */
Storm.Form.start = function () {
	this.onStart();

	// Проверим параметры
	this.startCheck();

	if( this.object ) {
		// Получим uri объекта, который будем редактировать
		var objectUri = this.object.getUri();

		// Проверим на счет одновременного редактирования одного и того же объекта в нескольких формах
		if( this.single && objectUri in this.urisBeingEdited ) {	
			return false;
		}

		this.urisBeingEdited[ objectUri ] = true;
	}

	
	this.create();
	return true;
};

/**
 * Клонирование формы на основе шаблона. Внутренний метод, и не должен быть переопределен в большинстве случаев.
 * Возвращает this, модифицируя this.form.
 */
Storm.Form.cloneForm = function () {
	// Клонируем исходную форму вместе с обработчиками. Удаляем id.
	this.form = this.form.clone( true ).hide().removeAttr( 'id' );
	return this;
};

/**
 * Установка обработчиков формы — нажатие кнопок, сабмит по нажатию enter и тому подобное.
 * Возвращает this;
 */
Storm.Form.bindFormActions = function () {
	// Сабмит
	this.form.find( this.submitButtonSelector ).bind( 'click', Function.delegate( this, function( event ) {
		event.preventDefault();
		this.submit();
	} ) );
	
	// Отмена
	this.form.find( this.cancelButtonSelector ).bind( 'click', Function.delegate( this, function( event ) {
		event.preventDefault();
		this.cancel();
	} ) );
	
	// Сабмит по enter в текстовых однострочных полях
	this.form.find( 'input[type=text]' ).bind( 'keypress', Function.delegate( this, function( event ) {
		if( event.keyCode == 13 ) {
			$( event.target ).trigger( 'change' );
			this.submit();
		}
	} ) );
	
	return this;
};

/**
 * Создание формы. Копируем шаблон формы, вставляет его в нужное место DOM, устанавливает обработчики на кнопки и поля ввода.
 * Форма пока остается скрытой, item для edit-форм остается без изменений, для create-форм клонируется.
 * Возвращает this
 */
Storm.Form.create = function () {
	// Клонируем форму
	this.cloneForm();
	
	// Вставляем форму в DOM
	switch( this.mode ) {
		case 'edit':
			if( this.formPlace ) {
				this.formPlace.prepend( this.form );
			} else if( this.item ) {
				this.item.before( this.form );
			} else {
				throw 'Для форм, работающих в режиме edit необходимо указывать параметр item или параметр formPlace';
			}
		break;
		
		case 'create':
			if( this.formPlace ) {
				this.formPlace.prepend( this.form );
			} else {
				throw 'Необходимо указать параметр formPlace';
			}
		break;
	}

	// TODO разобраться, зачем клонировать item прямо сейчас. Нельзя заниматься этим уже после того, как прошел submit и получены ответные данные?
/* Пробуем перевезти это в fillItem
	if( this.mode == 'create' && this.item ) {
		// Клонируем объект, который будет добавлен вместо формы. Удаляем id.
		this.item = this.item.clone( true ).hide().removeAttr( 'id' );
		
		if( this.itemPlace ) {
			this.itemPlace.prepend( this.item );
		} else {
			this.formPlace.prepend( this.item );
		}
	}
*/
	
	// Вешаем обработчики submit и cancel
	this.bindFormActions();

	this.onCreate( this.form );
	
	// Запускаем форму в работу
	this.launch();
};

/**
 * Запуск формы в работу
*
*
 */
Storm.Form.launch = function () {
	// Выполняем пользовательские штучки
	this.onLaunch( this.form );
	
	// Следующий этап в жизни формы произойдет при получении данных для начального заполнения формы.
	if( this.object ) {
		if( this.mode === 'create' ) {
			Storm.structure( this.object, Function.delegate( this, this.loadData ), Function.delegate( this, this.destroy ) );
		} else if( this.mode === 'edit' ) {
			Storm.retrieve( this.object, Function.delegate( this, this.loadData ), Function.delegate( this, this.destroy ) );
		}
	} else {
		this.loadData( {} );
	}

	return this;
};

/**
 * Загрузка начальных данных формы
 */
Storm.Form.loadData = function ( data ) {
	// Вызываем пользовательскую обработку загруженных данных
	this.onLoadData( data );

	// Сохраняем данные во внутреннюю переменную
	this.loadedData = data;
	
	// Заполняем форму
	this.fill();
	
	return this;
};

/**
 * Обнаружение полей ввода в форме, которые будут использоваться для редактирования данных.
 * Обнаруженные поля можно отфильтровать или расширить в пользовательском обработчике onFetchInputs
 */
Storm.Form.fetchInputs = function () {
	var inputs = {};
	
	// Находим все поля ввода формы по селектору
	if( this.form ) {
		this.form.find( this.formInputSelector ).each( function() {
			var name = $( this ).attr( 'name' );
			if( name ) {
				if(inputs[ name ]) {
					if( inputs[ name ] instanceof Array ) {
						inputs[ name ].push( this );
					}
					else {
						inputs[ name ] = [ inputs[ name ], this ];
					}
				}
				else {
					inputs[ name ] = this;
				}
			}
		} );
	}
	
	// Пользовательский обработчик
	this.onFetchInputs( inputs );
	
	// Сохраняем список полей
	this.inputs = inputs;
	
	return this;
};

/**
 * Заполнение формы данными
 * Возвращает this
 */
Storm.Form.fill = function () {
	// Прочитаем поля ввода
	this.fetchInputs();

	if( this.form ) {

		// Заполним поля ввода начальными данными	
		for( var name in this.loadedData ) {
			if( this.loadedData.hasOwnProperty( name ) ) {
				var value = this.loadedData[ name ];
				
				// Заполняем поле ввода
				if( name in this.inputs ) {
					if( this.inputs[ name ] instanceof Array ) {
						// Массив полей - radio
												
						for( var n in this.inputs[ name ] ) {
							if( this.inputs[ name ].hasOwnProperty( n ) ) {
								
								var input = $( this.inputs[ name ][ n ] );			
								
								if( input.attr( 'type' ) == 'radio') {
									if(value == input.val()) {
										input.attr('checked', 'checked');
										break;
									}
								}								
								
							}
						}
						
						continue;
					}
				
					var input = $( this.inputs[ name ] );
					
					// Для полей типа ENUM, помещаемых в select, автоматически заполняем этот select
					if( input.attr( 'tagName' ) == 'SELECT' && value && typeof value === 'object' && value['values'] instanceof Array ) {
						input.html( '' );
						$( value['values'] ).each( function( i, n ) {
							$( '<option></option>' ).attr( 'value', n ).text( n ).appendTo( input );
						} );
						value = value['value'];
					}
					// Чекбоксы требуют особого обращения
					else if( input.attr( 'type' ) == 'checkbox') {
						if(value) {
							input.attr('checked', 'checked');
						}
					}
					// Radiobutton тоже
					else if( input.attr('type') == 'radio') {
						if(value == input.val()) {
							input.attr('checked', 'checked');
						}
					}
					else {
						// Заполняем поле ввода
						input.val( value || '' );
					}

					
					// TODO добавить куда-то сюда обработку StormImageDbField - они тоже приходят в виде объекта, и что-то с ним надо делать
				}
				
				// Заполняем текст
				try{
					this.form.find( '[text='+name+']' ).text( value || '' );
				} catch( e ) {};
				// Заполняем html-текст
				try{
					this.form.find( '[html='+name+']' ).html( value || '' );
				} catch( e ) {};
			}
		}
	}
	
	// Пользовательский обработчик
	this.onFill( this.form, this.loadedData );
	
	// Форма заполнена, показываем ее
	this.show();
	
	return this;
};


/**
 * Показ формы. Прячет item в режиме edit и отображает саму форму.
 * Перед показом собственно формы вызывает пользовательский обработчик
 */
Storm.Form.show = function () {
	// В режиме edit прячем item
	if( this.mode === 'edit' && this.item ) {
		this.item.hide();
	}
	
	// Показ формы
	this.onShow( this.form );
	
	// Показываем саму форму
	this.form.show();
	
	// Триггерим показ полей ввода
	for( var name in this.inputs ) {
		if( this.inputs.hasOwnProperty( name ) ) {
			$( this.inputs[ name ] ).trigger( 'show' );
		}
	}

    // Обработка табов
    this.form.find('.tabs > li > a').click(function(){
        var tab_name = this.hash.slice(1);

        $(this).parents('.tabs').find('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $(this).parents('.a-unit-form').find('.m-tab-active').removeClass('m-tab-active');
        $(this).parents('.a-unit-form').find('.tab-'+ tab_name).addClass('m-tab-active');

        return false;
    });

	// 	В этот момент форма отправляется в свободное плавание; ждем нажатия submit или cancel пользователем
	return this;
};

/**
 * Получение данных, введенных пользователем в форму. Заполняет внутреннюю переменную this.data
 * Возвращает this
 */
Storm.Form.fetchData = function () {
	var data = {};
	
	// Вытаскиваем значения полей
	for( var name in this.inputs ) {
		if( this.inputs.hasOwnProperty( name ) ) {
			// Массив полей - radio
			if( this.inputs[ name ] instanceof Array ) {
				for( var n in this.inputs[ name ] ) {
					if( this.inputs[ name ].hasOwnProperty( n ) ) {
						
						var input = $( this.inputs[ name ][ n ] );			
						
						if( input.attr( 'type' ) == 'radio' && input.attr('checked') ) {
							data[name] = input.val();
							break;
						}
					}
				}
				
				continue;
			}
		
			// Если чекбокс не отмечен, отправим на сервер false
			if( $( this.inputs[ name ] ).attr('type') == 'checkbox' && ! $( this.inputs[ name ] ).attr('checked')) {
				data[ name ] = 0;
			}
			// Если radiobutton не отмечена, продолжим искать отмеченную =)
			else if( $( this.inputs[ name ] ).attr('type') == 'radio' && ! $( this.inputs[ name ] ).attr('checked') ) {
				continue;
			}
			// Если это ричтекст, тригернем генерацию кода
			else if ( $( this.inputs[ name ] ).attr('rich') == 'yes' ) {
				$( this.inputs[ name ] ).trigger('update');
				data[ name ] = $( this.inputs[ name ] ).val();
			}
			else {
				data[ name ] = $( this.inputs[ name ] ).val();
			}
		}
	}

	// Вызываем пользовательскую обработку
	this.onFetchData( this.form, data );
	
	this.data = data;
	
	return this;
};


/**
 * Обработчик нажатия кнопки submit. Получает данные, которые пользователь ввел в форму, оправляет их на сервер для сохранения
 * Возвращает this
 */
Storm.Form.submit = function () {
	// Получаем данные, которые пользователь ввел
	this.fetchData();
	
	if( this.object ) {
		if( this.mode === 'create' ) {
			Storm.create( this.object, this.data, Function.delegate( this, this.processResponse ) );
		} else if( this.mode === 'edit' ) {
			Storm.update( this.object, this.data, Function.delegate( this, this.processResponse ) );
		}
	} else {
		this.processResponse( {} );
	}
	
	return this;
};

/**
 * Обработка данных, полученных от сервера
 * response - объект с данными, которые передал сервер
 * Возвращает this
 */
Storm.Form.processResponse = function ( response ) {
	// Сохраняем ответ сервера
	this.response = response;
	
	// Вызываем пользовательский обработчик
	this.onSubmit( this.form, this.data, this.response );
	
	// И переходим к заполнению элемента, который нужно показать вместо формы
	this.fillItem( this.response );
	
	return this;
};


Storm.Form.createItem = function( response ) {

	if( this.mode == 'create' && this.item ) {
		// Клонируем объект, который будет добавлен вместо формы. Удаляем id.
		this.item = this.item.clone( true ).hide().removeAttr( 'id' );
		
		if( this.itemPlace ) {
			this.itemPlace.prepend( this.item );
		} else {
			this.formPlace.prepend( this.item );
		}
	}

	// Заполняем элемент
	if( this.item && response ) {
		for( var name in response ) {
			if( response.hasOwnProperty( name ) ) {
				try{
					this.item.find( '[stormText=' + name + ']' ).text( this.response[ name ] );
				} catch( e ) {};
				try{
					this.item.find( '[stormHtml=' + name + ']' ).html( this.response[ name ] );
				} catch( e ) {};
			};
		};
	};
}

/**
 * Заполнение элемента данными с сервера, готовим его к отображению вместо формы
 * Возвращает this
 */
Storm.Form.fillItem = function ( response ) {
	
	this.createItem( response );
	
	// Вызываем пользовательскую обработку
	this.onFillItem( this.item, response );
	
	// Прячем форму	
	this.hide( true );
	
	return this;
};

/**
 * Обработка нажатия кнопки отмены
 * Возвращает this
 */
Storm.Form.cancel = function () {
	this.onCancel( this.form );
	
	if(this.mode === 'create' && this.item) {
		this.item.remove();
	}
	
	this.hide( this.mode !== 'create' );
	return this;
};

/**
 * Скрытие формы
 * showItem - показывать item вместо формы после ее скрытия или нет
 * Возвращает this
 */
Storm.Form.hide = function ( showItem ) {
	this.form.hide();
	this.onHide( this.form );
	
	if( this.item && showItem ) {
		this.item.show();
	}
	
	this.onShowItem( this.item );

	this.destroy();

	return this;
};

/**
 * Уничтожеие формы
 * Возвращает this
 */
Storm.Form.destroy = function () {
	// Очищаем хэш открытых объектов
	var objectUri = this.object.getUri();
	if( objectUri in this.urisBeingEdited ) {
		delete this.urisBeingEdited[ objectUri ];
	}
	// Обработчик
	this.onDestroy( this.form );

	// Удаляем форму	
	this.form.remove();
	
	return this;
};