var Madone = window.Madone = {};

// URI к системе управления
Madone.uri = /(\/[^\/]+)/.test( location.pathname ) ? RegExp.$1 : '';

// Создание Визуального редактора
Madone.createRichTextEditor = function( name, options ) {
	if( ! options ) {
		options = {};
	}
	if( ! ( 'height' in options ) ) {
		options.height = 300;
	}
	if( ! ( 'value' in options ) ) {
		options.value = '';
	}

	CKEDITOR.config.customConfig = '/media/ckeditor.config.js?20110906';
	
	var editor = CKEDITOR.replace( name );

	editor.config.height = options.height;
	return editor;
};

Madone.useRichTextEditorAPI = function( instanceName, callback ) {
	( function wait () {
		try {
			var instance = CKEDITOR.instances[instanceName];
			callback.call( instance );
		} catch( e ) {
			setTimeout( wait, 200 );
		}
	} )();
};

Madone.RichTextEditorSetHTML = function( name, html ) {
	Madone.useRichTextEditorAPI( name, function() {
		this.setData( html );
	} );
};

Madone.RichTextEditorGetHTML = function( name ) {
	return CKEDITOR.instances[instanceName].getData();
};




Madone.nestedSortableOptions = Object.create( Object.Extendable ).extend({
    disableNesting: 'no-nest',
    forcePlaceholderSize: true,
    handle: 'div',
    helper:	'clone',
    items: 'li',
    opacity: .6,
    placeholder: 'placeholder',
    revert: 100,
    tabSize: 15,
    tolerance: 'intersect',
    toleranceElement: '> div'
});

Madone.addAjaxLoader = function( object, position ) {
	object = $( object );
	var loaderHtml = '<img class="ajax-loader" src="' + '/static/i/admin/ajax-loader-e2e9fe.gif" />';
	switch( position ) {
		case 'inside':
			if( ! object.find( '.ajax-loader' ).size() ) {
				object.append( $( loaderHtml ) );
			}
			break;
		default:
			if( ! object.next( '.ajax-loader' ).size() ) {
				object.after( $( loaderHtml ) );
			}
			break;
	}
	return this;
};

Madone.removeAjaxLoader = function( object ) {
	$( object ).next( '.ajax-loader' ).remove().end().find( '.ajax-loader' ).remove();
	return this;
};

Madone.enableRichTextareas = function( immediate ) {
	var fckCnt = 1;
	$( 'textarea[rich=yes]' ).bind( 'show', function() {
		var $this = $( this );
		if( ! $this.data( 'fck' ) ) {
			var id = 'fck' + fckCnt++;
			$this.attr( 'id', id ).data( 'fck', true );
			var inputHeight = $this.height();
			Madone.createRichTextEditor( id, inputHeight ? { height: inputHeight } : null );
			Madone.useRichTextEditorAPI( id, function() {
				this.on( 'selectionChange', function() {
					this.updateElement();
				});
			});
		}
	}).bind('update', function(){
		var $this = $( this );
		var id = $this.attr( 'id' );
		Madone.useRichTextEditorAPI( id, function() {
			this.updateElement();
		});
	});
	
	if( immediate !== false ) {
		$( 'textarea[rich=yes]:visible' ).trigger( 'show' );
	}
	
	return this;
};

Madone.enableDatepickers = function( immediate ) {
	$( 'input[datepicker=yes]' ).bind( 'show', function() {
		$( this ).datepicker( {
			dateFormat: 'dd.mm.yy',
			duration: '',
			firstDay: 1,
			dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
			monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
		} );
	} );
	
	if( immediate !== false ) {
		$( 'input[datepicker=yes]:visible' ).trigger( 'show' );
	}
	
	return this;
};

Madone.uploadify = Object.create( Object.Extendable ).extend({
	multi 		  : true,
	uploader  	  : '/media/uploadify-2.1.0/scripts/uploadify.swf',
	cancelImg 	  : '/media/uploadify-2.1.0/cancel.png',
	buttonImg	  : '/static/i/admin/upload-button.png',
	width		  : 147,
	height		  : 24,
	auto      	  : true,
	onOpen		  : function(){$('.uploadifyQueue').css('display', 'block')},
	onAllComplete : function(){$('.uploadifyQueue').css('display', 'none')},
	scriptAccess  : 'always'
});



// Галерея
Madone.ImageGallery = Object.create( Storm.Form ).extend({
	stormModel: '',
	form: $("<form>").addClass("a-unit-form").addClass("form-stacked").append(
			'<h2 text="title">Заголовок:</h2>' +
			'<div class="block"><div class="gallery"></div></div>' +
			'<div class="block">' +
				'<input type="file" name="image" class="uploadify" />' +
			'</div>' +
			'<div class="actions">' +
				'<button class="cancel btn">Закрыть</button>' +
			'</div>'
	),
	getItemFormTemplate: function(){
		return $("<span>").addClass("gallery-form")
					.append(
						'<img class="image" src=""><div class="form">' +
						'<div><label>Название:</label><textarea class="width-100" name="title"></textarea></div>' +
							'<div class="block">' +
								'<button class="submit btn primary">Сохранить</button>' +
								'<button class="cancel btn">Отмена</button>' +
							'</div>' +
						'</div>'
					)
	},
	onEditClick: function(event, Form, button) {
		console.dir(button);
		var item = $( button ).parents( '.thumb' );
		var obj = Object.create( Storm.Form ).extend( {
			object: Storm.buildPath( button ),
			form: Form.getItemFormTemplate(),
			item: $( button ).parents( '.thumb' ),
			onFill: function ( form, data ) {
				form.find( '.image' )
				.attr( 'src', data.image.cms ? data.image.cms.uri : '/static/i/admin/1x1.gif' )
				.attr( 'width', data.image.cms ? data.image.cms.width : 50 )
				.attr( 'height', data.image.cms ? data.image.cms.height : 50 );
			},
			onFillItem: function( item, data ) {
				item.find( '.image' ).attr( 'title', data.title || '' );
			},
			onStart: function() {
				console.dir(this.object);
			},
			onCreate: function(form) {
				console.log(2);
			}
		});
		obj.start();
	},
	getItemTemplate: function () {
		var Obj = this;
		var template = $("<a>").addClass('thumb').append( 
			$("<span>").addClass('control').append(
				$("<img>").attr('title', 'Редактировать')
						.attr('src', '/static/i/admin/icons/16/pencil.png?ffffff')
						.attr('width', '16')
						.attr('height', '16')
						.addClass('edit-item')
						.click(function(event){
							Obj.onEditClick(event, Obj, this);
						})
			).append(
				$("<a>").addClass('zoom-item-a').append(
					$("<img>").attr('title', 'Увеличить')
						.attr('src', '/static/i/admin/icons/16/magnifier.png?ffffff')
						.attr('width', '16')
						.attr('height', '16')
						.addClass('zoom-item')
				)
			).append(
				$("<img>").attr('title', 'Удалить')
						.attr('src', '/static/i/admin/icons/16/cross.png?ffffff')
						.attr('width', '16')
						.attr('height', '16')
						.addClass('delete-item')
						.click(function(){
							var img = $( this ).parents( '.thumb' );
							// Затеняем все изображения кроме удаляемого, чтобы дать понять пользователю, что он удаляет
							var other = $( this ).parents( '.gallery' ).children().not( img );
							other.addClass( 'semitransparent' );
							if( confirm( 'Удалить изображение?' ) ) {
								Storm.remove( Storm.buildPath( this ), function () {
									var list = img.parents( '.gallery' );
									img.remove();
									if( ! list.find( '.thumb, .gallery-form' ).size() ) {
										list.addClass( 'empty' ).text( 'Изображения не загружены.' );
									}
								} );
							}
							other.removeClass( 'semitransparent' );
						})
			)
		).append(
			$("<img>").addClass("image")
		);
		
		return template;
	},
	
	onCreate: function( form ) {
		var Obj = this;
		// Сортировка картинок в форме
		form.find( '.gallery' )
			.attr( 'stormModel', Obj.stormModel )
			.sortable( {
			stop: function ( e, ui ) {
				var objects = {};
				form.find( '.gallery .thumb' ).each( function( i ) {
					objects[ $( this ).attr( 'stormObject' ) ] = { position: i + 1 };
				} );
				Storm.update( Obj.stormModel, objects );
			}
		});
	},
	onShow: function(form) {
		var Obj = this;
		
		form.find('.uploadify').attr('id', 'uploadify');
		form.find('#uploadify').uploadify(Object.create(Madone.uploadify).extend({
			scriptData : {section: Obj.loadedData.id, PHPSESSID: Obj.PHPSESSID},
			fileDataName: 'image',	
			script    : Storm.getPath( Obj.stormModel ).getUri() + '/create/',
			onComplete  : function (event, queueID, fileObj, response, data) {
				var r = $.evalJSON(response);
				Obj.appendItem( r.data );
			}
		}));
	},

	onFill: function ( form, data ) {
		var Obj = this;
		
		if( Madone.language !== 'ru' ) {
			form.find( 'h2:first' ).append( $( '<i class="ru-hint">(' + data.RU.title + ')</i>' ) );
		}
		this.form.find( '.gallery' ).addClass( 'loading' );
		// На заполнение формы загружаем список картинок и выводим их
		var query = Object.create( Storm.Query ).use( 'filter', { section: data.id } ).use( 'order', 'position' );
		Storm.retrieve( Obj.stormModel, query.get(), Function.delegate( this, function ( data ) {
			this.form.find( '.gallery' ).removeClass( 'loading' );
			if( Object.typeOf( data ) === 'array' && data.length ) {
				for(var i = 0; i < data.length; i++) {
					this.appendItem( data[i] );
				}
				
				/* Зумилка картинок галереи */
				$('.zoom-item-a').fancybox();
			} else {
				this.form.find( '.gallery' ).removeClass( 'loading' ).addClass( 'empty' ).text( 'Изображения не загружены.' );
			}
		} ) );
	},
	
	// Уникальный метод формы — добавление изображения в список, используется при отображении списка изображений
	// на сервере и добавлении загруженных изображений.
	appendItem: function ( data ) {
			// Удаляем надпись «Нет изображений», если это изображения будет первым в списке
			if( ! this.form.find( '.gallery .thumb, .gallery .gallery-form' ).size() ) {
				this.form.find( '.gallery' ).html('').removeClass( 'empty' );
			}
			var img = this.getItemTemplate();
			this.form.find( '.gallery' ).append( img );
			img.attr( 'stormObject', data.id );
			img.find( '.image' )
			.attr( 'src', data.image.cms ? data.image.cms.uri : '/static/i/admin/1x1.gif' )
			.attr('largeSrc', data.image.large.uri)
			.attr( 'title', data.title || '' )
			.attr( 'width', data.image.cms ? data.image.cms.width : 50 )
			.attr( 'height', data.image.cms ? data.image.cms.height : 50 );
			
			img.find('.zoom-item-a')
			.attr('href', data.image.large.uri)
			.attr('title', data.title || '' )
			.attr('rel', 'section_' + data.section);

			img.show();
	}
});

// Видеогалерея
Madone.VideoGallery = Object.create( Madone.ImageGallery ).extend({
onEditClick: function(event, Form, button) {
		Object.create( Storm.Form ).extend( {
			object: Storm.buildPath( button ),
			form: Form.getItemFormTemplate(),
			item: $( button ).parents( '.thumb' ),
			onFill: function ( form, data ) {
				var desiredWidth = 120;
				form.find( '.image' )
				.attr( 'src', data.movie ? data.movie.preview_uri : '/static/i/admin/1x1.gif' )
				.attr( 'width', data.movie ? desiredWidth : 120 )
				.attr( 'height', data.movie ? data.movie.height * desiredWidth / data.movie.width : 120 );
			},
			onFillItem: function( item, data ) {
				item.find( '.image' ).attr( 'title', data.title || '' );
			}
		}).start();
	},
	appendItem: function ( data ) {
		if( ! this.form.find( '.gallery .thumb, .gallery .gallery-form' ).size() ) {
			this.form.find( '.gallery' ).html('').removeClass( 'empty' );
		}
		var img = this.getItemTemplate();
		this.form.find( '.gallery' ).append( img );
		img.attr( 'stormObject', data.id );
		var desiredWidth = 120;
		img.find( '.image' )
		.attr( 'src', data.movie ? data.movie.preview_uri : '/static/i/admin/1x1.gif' )
		.attr( 'title', data.title || '' )
		.attr( 'width', data.movie ? desiredWidth : 120 )
		.attr( 'height', data.movie ? data.movie.height * desiredWidth / data.movie.width : 120 );
		img.show();
	}
});