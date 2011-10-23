/**
 * Обработчик сетки представления данных в админке
 */
Storm.Grid = Object.create( Object.Extendable ).extend( {
});

// Элементы
Storm.Grid.items = [];

Storm.Grid.model = 	// Имя модели, объекты которой выводим
Storm.Grid.item = 	// Шаблон элемента
Storm.Grid.place = 	// Место для отображения в DOM
Storm.Grid.customHandlers = // Обработчики дополнительных кнопок
Storm.Grid.nestedSortable = // Вложенная сортировка
Storm.Grid.simpleSortable = // Простая сортировка
Storm.Grid.itemForm = // Объект формы элементов
null;

// Сообщение об отсутствии элементов в grid
Storm.Grid.emptyText = "Ничего нет";

/**
 * Объект описывающий каким образом должны заполняться элементы при отображении
 * Значение по ключу может быть либо строкой, либо функцией, например:
 * {
 *		'title': 'title',
 *		'description': function( data ) {
 *			return data.text + " " + data.date;
 *		}
 */
Storm.Grid.itemDataMapper = {
	'title': 'title',
	'description': 'description'
}

Storm.Grid.onGridRendered = function () {};
Storm.Grid.onItemAdded = function ( item ) {};
Storm.Grid.onItemRemoved = function () {};		

// Обработчики для кнопок элементов
// Обработчик кнопки вкл/выкл
Storm.Grid.enabledHandler = function( e ) {
	Storm.toggle( Storm.buildPath( this ), Function.delegate( this, function ( data ) {
		if(data.enabled) {
			$( this ).parents('.a-unit-body:first').removeClass('disabled');
			$( this ).attr( 'src', Madone.uri+'/static/i/icons/16/lamp-on.png');
		}
		else {
			$( this ).parents('.a-unit-body:first').addClass('disabled');			
			$( this ).attr( 'src', Madone.uri+'/static/i/icons/16/lamp-off.png?effbff' );
		}
	}));
};
// Обработчик кнопки редактирования
Storm.Grid.editHandler = function( e ) {};
// Обработчик кнопки удаления
Storm.Grid.deleteHandler = function( e ) {
	var title = $( this ).parents( '.a-unit:first' ).find( 'h2:first' ).text();
    
    if( confirm( 'Вы действительно хотите удалить «' + title  + '»?' ) ) {
		var nested = $( this ).parents( '.a-unit:first' ).find( '.a-unit' ).size();
        if( nested ) {
            if( ! confirm( Mad.decline( nested, '',
				'«' + title +'» содержит еще %n вложеный элемент.',
				'«' + title +'» содержит еще %n вложеных элемента.',
				'«' + title +'» содержит еще %n вложеных элементов.' ) +
				' Вы действительно хотите удалить их все?' ) )
            return false;
        }

		Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
			$( this ).parents( '.a-unit:first' ).remove();
		}));
	}
};

// Инициализация сортировки вложенной структуры
Storm.Grid.initNestedSortable = function(grid) {
	this.place.NestedSortableDestroy().NestedSortable( Object.create( Madone.NestedSortableOptions ).extend( {
		onChange: function ( serialized ) {
			Storm.reorder( 
				grid.model, 
				{ 	id: grid.nestedSortable.rootID, 
					children: serialized[0].o[ grid.place.attr('id') ] 
				} 
			);
			
			$(grid).trigger('itemsReordered');
		}
	}));

	this.place.find( grid.nestedSortable.movable ).addClass( 'movable' );
}

Storm.Grid.bindHandlers = function( grid ) {

	if( this.nestedSortable && Object.typeOf( this.nestedSortable ) === 'object' ) {		
		$(grid).bind('gridRendered', function(e){
			this.initNestedSortable( this );
			
			if( this.itemForm ) {
				// Для того чтобы сортировка работала в ИЕ ее необходимо переинициализировать каждый раз при изменении дерева
				// Для этого нужно модифицировать обработчик onShowItem у формы
				if(this.itemForm.onShowItem) {
					var onShowItemOld = Function.delegate(this.itemForm, this.itemForm.onShowItem);
					
					this.itemForm.onShowItem = Function.delegate( this, function( item ) {
						onShowItemOld();					
						this.initNestedSortable(this);
					});
				}
				else {
					this.itemForm.onShowItem = Function.delegate( this, function( item ) {
						this.initNestedSortable(this);
					});
				}
			}		
		});
	}
	else if( this.simpleSortable ) {
		this.place.sortable( {
			stop: function ( e, ui ) {
				var objects = {};
				grid.place.find( '.stormGridItem' ).each( function( i ) {
					objects[ $( this ).attr( 'stormObject' ) ] = { position: i + 1 };
				});
				Storm.update( grid.model, objects );
			}
		});
	}

	this.item.find( '.enabled' ).click( this.enabledHandler );
	this.item.find( '.edit' ).click( this.editHandler );
	this.item.find( '.delete' ).click( function(e){
		var handler = Function.delegate(this, grid.deleteHandler);
		
		handler();
		
		$(grid).trigger('itemRemoved');
	});
	
	// Кастомные обработчики
	if(this.customHandlers) {
		for(var i in this.customHandlers) {
			this.item.find(i).bind('click', this.customHandlers[i]);
		}
	}
};

// Удаление элемента
Storm.Grid.removeItem = function() {
	this.onItemRemoved();
};

// Наполнение элемента
Storm.Grid.fillItem = function( item, data ) {
	for(var i in this.itemDataMapper) {
		if( typeof this.itemDataMapper[i] !== 'function' ) {
			item.find('[stormGrid=' + i + ']').html(data[ this.itemDataMapper[i] ]);
		}
		else {
			item.find('[stormGrid=' + i + ']').html( this.itemDataMapper[i]( data ) );
		}
	}
	
	item.find('.enabled').attr('src', data.enabled ? Madone.uri+'/static/i/icons/16/lamp-on.png' : Madone.uri+'/static/i/icons/16/lamp-off.png');
	
	if(! data.enabled ) {
		item.find('.a-unit-body').addClass('disabled');
	}
	
	return item;
};

// Добавление элемента
Storm.Grid.addItem = function(data) {
	if(this.place.hasClass('empty')) {
		this.place.removeClass('empty').text('');
	}	
	
	var parent = this.place;

	if(arguments[1]) {
		parent = arguments[1];
	}
	
	if(this.item) {
		var newItem = this.item.clone(true).removeAttr('id');

		newItem.addClass('stormGridItem');
		newItem.attr('stormObject', data.id);
		newItem.attr('id', "a-unit-" + data.id);
		
		newItem = this.fillItem(newItem, data);
		
		newItem.appendTo(parent).show();
		
		this.onItemAdded(newItem, data);
		
		return newItem;
	}
};

Storm.Grid.renderItems = function(items, parent) {
	for( var i in items ) {
		var newItem = this.addItem( items[i], parent );
		
		if(items[i].hasOwnProperty('children')) {
			this.renderItems( 
				items[i].children, 
				$("<span/>").addClass('a-unit-list').appendTo(newItem) 
			);
		}
	}
	
	return this;
};

Storm.Grid.render = function() {

	if(this.items.length > 0) {
		this.renderItems(this.items, this.place);
	}
	else {
		this.place.addClass('empty').text(this.emptyText);
	}
	
			
	this.onGridRendered();
	
	$(this).trigger('gridRendered');
	
	return this;
};

Storm.Grid.start = function() {
	this.bindHandlers(this);

	if(this.itemForm) {
		var grid = this;
		
		this.itemForm.createItem = Function.delegate(this.itemForm, function(response) {
			grid.addItem(response, grid.place);
		});
	}

	this.render();

	return this;
};
