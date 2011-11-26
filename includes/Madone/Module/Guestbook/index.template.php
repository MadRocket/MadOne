<style type="text/css">

.images {
	display: block;
	margin:0;
	background:#fff;
	-moz-border-radius:5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
	border: 1px solid #d59f35;
	padding:2px;
	overflow: hidden;
	~display: inline-block;
	~padding-bottom: 6px;
	~overflow: visible;
}

.images.loading {
	height: 60px;
	background: #fff url('/static/i/admin/items-loading-bg.gif') center center no-repeat;
}

.images.empty {
	height: 60px;
	line-height: 60px;
	font-style: italic;
	color: #777;
	text-align: center;
}

.images .img {
	margin: 4px;
	float: left;
}

.images .img img {
	cursor: move;
	vertical-align:middle;
	border: 1px solid #ddd;
}

.images .control {
	position: absolute;
	display: none;
}

.images .control img {
	background: #fff;
	margin: 8px 0 0 8px;
	border: 4px solid #fff;
	cursor: pointer;
}

.images .hover .control {
	display: block;
}

.semitransparent {
	opacity: 0.2;
	-moz-opacity: 0.2;
	filter: alpha(opacity=20);
}

.img-form {
	float: left;
	background: #fff;
	border: 1px solid #d59f35;
	margin: 3px;
	padding: 0;
	
	-webkit-box-shadow: 0px 0px 8px #ccc;
	-moz-box-shadow: 0px 0px 8px #ccc;	
	-box-shadow: 0px 0px 8px #ccc;		
}

.img-form img {
	float: left;
	~margin-left: -3px;
}

.img-form .form {
	width: 300px;
	float: left;
	margin: 0px 5px;
	padding-top: 3px;
}

.img-form .form textarea {
	height: 50px;
}

</style>

<div class="module-content">
	
	<div class="createFormPlace"></div>
	
	<div class="a-units" stormModel="MadoneGuestbookRecord">

	<? foreach( $this->paginator->getObjects() as $i ): ?>
	<div class="a-unit">
	<div class="a-unit-body<?= $i->enabled ? '' : ' disabled'?>" stormObject="<?=$i->id?>">
		<div class="actions">
			<img title="Открыть список изображений" class="image-list" width="16" height="16" src="/static/i/admin/icons/16/image.png"/>
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $i->enabled ? 'on' : 'off'?>.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<h2><?= $i->name ?> <?= $i->email ?></h2>
		<p><?= $i->date ?></p>
	</div>
	</div>
	<? endforeach ?>

	</div>
</div>

<div id="record-form" class="a-unit-form" style="display:none;">
	<div class="block"><label>Имя:</label><input class="width-100" type="text" name="name"/></div>
	<div class="block"><label>Электронная почта:</label><input class="width-100" type="text" name="email"/></div>
	<div class="block"><label>Дата публикации:</label><input datepicker="yes" class="width-100" type="text" name="date"/></div>
	<div class="block"><label>Текст отзыва:</label><textarea class="width-100 height-200" name="text"></textarea></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>

<div id="images-form" class="a-unit-form" style="display:none;">
	<h2><span text="name">Заголовок</span> <span text="email">e@mail</span></h2>
	<div class="block"><div class="images" stormModel="MadoneGuestbookImage"></div></div>
	<div class="block">
		<button class="upload small-styled-button"><b><b>Загрузить изображение</b></b></button>
	</div>
	<div class="block">
		<button style="margin-left:0;" class="cancel small-styled-button"><b><b>Закрыть</b></b></button>
	</div>
</div>

<div class="a-unit" id="record-template" style="display:none;">
    <div class="a-unit-body">
        <div class="actions">
			<img class="image-list" title="Открыть список изображений" width="16" height="16" src="/static/i/admin/icons/16/image.png"/>
			<img class="edit" title="Редактировать" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img class="enabled" title="Включить/выключить" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img class="delete" title="Удалить" width="16" height="16" alt="Удалить" src="/static/i/admin/icons/16/cross.png"/>
        </div>
        <h2>Название раздела</h2>
        <p>name</p>
    </div>
</div>

<span id="image-template" class="img" style="display:none;"><div class="control"><img title="Редактировать" class="edit-image" src="/static/i/admin/icons/16/pencil.png" width="16" height="16" /><img class="delete-image" title="Удалить" src="/static/i/admin/icons/16/cross.png" width="16" height="16" /></div><img class="image" src="" /></span>

<span id="image-form" class="img-form" style="display:none;">
<img class="image" src=""><div class="form">
<div><label>Название изображения:</label><textarea class="width-100" name="title"></textarea></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>
</span>

<script>
$( function () {
	// Инициализация (и переинициализация) сортировки дерева разделов
	function initSortable() {
	}

	// Сортировку запускаем сразу же
	initSortable();

	// Вкл/выкл раздела
	$( '.enabled' ).click( function( e ) {
		Storm.toggle( Storm.buildPath( this ), Function.delegate( this, function ( data ) {
			if(data.enabled) {
				$( this ).parents('.a-unit-body:first').removeClass('disabled');
				$( this ).attr( 'src', '/static/i/admin/icons/16/lamp-on.png');
			}
			else {
				$( this ).parents('.a-unit-body:first').addClass('disabled');			
				$( this ).attr( 'src', '/static/i/admin/icons/16/lamp-off.png' );
			}	
		}));
	});

	// Удаление раздела
	$( '.delete' ).click( function( e ) {
		var title = $( this ).parents( '.a-unit:first' ).find( 'h2:first' ).text();
        if( confirm( 'Вы действительно хотите удалить отзыв?' ) ) {
			Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
				$( this ).parents( '.a-unit:first' ).remove();
			} ) );
		}
	} );
	
	var recordForm = Object.create( Storm.Form ).extend( {
		form: $( '#record-form' ),
		onFillItem: function ( item, data ) {
			item.find( 'img.enabled' ).attr( 'src', data.enabled ? '/static/i/admin/icons/16/lamp-on.png' : '/static/i/admin/icons/16/lamp-off.png' );
			if( ! data.enabled ) {
				item.find( '.a-unit-body' ).addClass( 'disabled' );
			}
			item.attr( 'stormObject', data.id );
			item.find( 'h2:first' ).html( data.name + ' ' + data.email );
			item.find( 'p:first' ).html( data.date );
		}
	} );

    // Создание раздела
    $( '.create-record' ).click( function ( e ) {
        e.preventDefault();
		Object.create( recordForm ).extend( {
			object:		'MadoneGuestbookRecord',
			formPlace:	$( '.createFormPlace' ),
			item:		$( '#record-template' ),
			itemPlace:	$( '#records' )
		} ).start();
	} );

    // Редактирование раздела
    $( '.edit' ).bind( 'click', function( event ) {
		event.preventDefault();
		Object.create( recordForm ).extend( {
			object:	Storm.buildPath( this ),
			item:	$( this ).parents( '.a-unit-body' )
		} ).start();
    } );

    // Форма списка картинок имеет собственную непростую функциональность.
    // Поэтому создаем объект на прототипе Storm.Form, расширяем его, и формы картинок будем порождать
    // от этой расширенной формы, а не от стандартной.
    var itemsForm = Object.create( Storm.Form ).extend( {
		form: $( '#images-form' ),

		onCreate: function( form ) {
			// Сортировка картинок в форме
			form.find( '.images' ).sortable( {
				start: function( e, ui ) {
					ui.item.removeClass( 'hover' );
				},
				over: function ( e, ui ) {
					ui.item.removeClass( 'hover' );
				},
				stop: function ( e, ui ) {
					var objects = {};
					form.find( '.images .img' ).each( function( i ) {
						objects[ $( this ).attr( 'stormObject' ) ] = { position: i + 1 };
					} );
					Storm.update( 'MadoneGuestbookImage', objects );
				}
			});

			// Загрузка картинки
			var This = this;	// В This будет ссылка на форму, чтобы из callback-ов можно было рулить
			form.find( '.upload' ).each( function () {
				var button = this;
				var uploadCount = 0;	// число выполняющихся сейчас загрузок файлов
				new AjaxUpload( this, {
						action: Storm.getPath( 'MadoneGuestbookImage' ).getUri() + '/create/',
						name: 'image',
						responseType: 'json',
						onSubmit : function ( file, ext ) {
							// Перед отправкой формы добавляем данные — id раздела, который берем из формы
							this.setData( { record: This.loadedData.id } );
							Madone.addAjaxLoader( button );
							++uploadCount;
						},
						onComplete: function ( file, r ) {
							if( ! --uploadCount ) {
								Madone.removeAjaxLoader( button );
							}
							if( r.success ) {
								This.appendImage( r.data );
							}
							else {
								alert( 'Ошибка при загрузке файла ' + file + '. ' + r.message );
							}
						}
					});
			});
		},

		onFill: function ( form, data ) {
			this.form.find( '.images' ).addClass( 'loading' );
			// На заполнение формы загружаем список картинок и выводим их
			var query = Object.create( Storm.Query ).use( 'filter', { record: data.id } ).use( 'order', 'position' );
			Storm.retrieve( 'MadoneGuestbookImage', query.get(), Function.delegate( this, function ( data ) {
				this.form.find( '.images' ).removeClass( 'loading' );
				if( Object.typeOf( data ) === 'array' && data.length ) {
					for(var i = 0; i < data.length; i++) {
						this.appendImage( data[i] );
					}
				} else {
					this.form.find( '.images' ).removeClass( 'loading' ).addClass( 'empty' ).text( 'Изображения не загружены.' );
				}
			} ) );
		},
		
		// Уникальный метод формы — добавление изображения в список, используется при отображении списка изображений
		// на сервере и добавлении загруженных изображений.
		appendImage: function ( data ) {
				// Удаляем надпись «Нет изображений», если это изображения будет первым в списке
				if( ! this.form.find( '.images .img, .images .img-form' ).size() ) {
					this.form.find( '.images' ).html('').removeClass( 'empty' );
				}
				var img = $( '#image-template' ).clone( true ).hide().removeAttr( 'id' );
				this.form.find( '.images' ).append( img );
				img.attr( 'stormObject', data.id );
				img.find( '.image' )
				.attr( 'src', data.image.cms ? data.image.cms.uri : '/static/i/admin/1x1.gif' )
				.attr( 'title', data.title || '' )
				.attr( 'width', data.image.cms ? data.image.cms.width : 50 )
				.attr( 'height', data.image.cms ? data.image.cms.height : 50 );
				img.show();
		}
    } );

    // Отображение нашей непростой формы списка изображений :)
    $( '.image-list' ).bind( 'click', function( event ) {
		Object.create( itemsForm ).extend( {
			object:	Storm.buildPath( this ),
			item:	$( this ).parents( '.a-unit-body' )
		} ).start();
    } );

	// Проявление кнопок редактирования изображения при наведении на него указателя
	$( '.img' )
		.mouseover(  function ( e ) { $( this ).addClass( 'hover' ); } )
		.mouseleave( function ( e ) { $( this ).removeClass( 'hover' ); } );

	// Удаление изображения
	$( '.delete-image' ).click( function ( e ) {
		var img = $( this ).parents( '.img' );
		// Затеняем все изображения кроме удаляемого, чтобы дать понять пользователю, что он удаляет
		var other = $( this ).parents( '.images' ).children().not( img );
		other.addClass( 'semitransparent' );
		if( confirm( 'Удалить изображение?' ) ) {
			Storm.remove( Storm.buildPath( this ), function () {
				var list = img.parents( '.images' );
				img.remove();
				if( ! list.find( '.img, .img-form' ).size() ) {
					list.addClass( 'empty' ).text( 'Изображения не загружены.' );
				}
			} );
		}
		other.removeClass( 'semitransparent' );
	} );

	// Редактирование изображения с помощью собственной формы :D
	$( '.edit-image' ).click( function ( e ) {
		Object.create( Storm.Form ).extend( {
			object: Storm.buildPath( this ),
			form: $( '#image-form' ),
			item: $( this ).parents( '.img' ),
			onFill: function ( form, data ) {
				form.find( '.image' )
				.attr( 'src', data.image.cms ? data.image.cms.uri : '/static/i/admin/1x1.gif' )
				.attr( 'width', data.image.cms ? data.image.cms.width : 50 )
				.attr( 'height', data.image.cms ? data.image.cms.height : 50 );
			},
			onFillItem: function( item, data ) {
				item.find( '.image' ).attr( 'title', data.title || '' );
			}
		} ).start();
	} );
	
} );

</script>
