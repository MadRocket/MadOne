<div class="module-buttons">
	<button class="create-unit styled-button"><b><b>Добавить позицию</b></b></button>
</div>

<div class="module-header"><h1><?= $this->section->title ?><? if( ! Madone::isCurrentLanguage( 'ru' ) && $this->section->RU->title ) :?> (<?= $this->section->RU->title ?>)<? endif ?></h1><a href="../">вернуться к списку разделов</a></div>

<div class="module-content">

	<div class="createFormPlace"></div>
	
	<div class="a-units" stormModel="MadoneShowcaseItem">
	
	<?foreach( $this->paginator->getObjects() as $i ):?>
	<div class="a-unit">
	<div class="a-unit-body<?= $i->enabled ? '' : ' disabled'?>" stormObject="<?=$i->id?>">
		<div class="actions">
			<img title="Открыть список видео-файлов" class="movie-list" width="16" height="16" src="/static/i/admin/icons/16/tv.png"/>
			<img title="Открыть список изображений" class="image-list" width="16" height="16" src="/static/i/admin/icons/16/image.png"/>
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $i->enabled ? 'on' : 'off'?>.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<h2><?= $i->title ? $i->title : "&nbsp;" ?><? if( ! Madone::isCurrentLanguage( 'ru' ) && $i->RU->title ) :?><i class="ru-hint">(<?= $i->RU->title ?>)</i><? endif ?></h2>
		<p><?= $i->price ? "{$i->price} руб" : 'цена не указана' ?></p>
	</div>
	</div>
	<?endforeach?>

</div>

<div id="item-form" class="a-unit-form" style="display:none;">
	<div class="block"><label>Название:</label><input class="width-100" type="text" name="title"/></div>
	<div class="block"><label>Цена:</label><input class="width-100" type="text" name="price"/></div>
	<div class="block"><label>Краткое описание:</label><textarea class="width-100 height-100" name="short_description"></textarea></div>
	<div class="block"><label>Полное описание:</label><textarea rich="yes" class="width-100 height-300" name="description"></textarea></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>

<div class="a-unit" id="item-template" style="display:none;">
	<div class="a-unit-body">
        <div class="actions">
			<img title="Открыть список видео-файлов" class="movie-list" width="16" height="16" src="/static/i/admin/icons/16/tv.png"/>
			<img title="Открыть список изображений" class="image-list" width="16" height="16" src="/static/i/admin/icons/16/image.png"/>
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
        </div>
        <h2>Название позиции</h2>
        <p>Цена, руб</p>
    </div>
</div>

<div id="images-form" class="a-unit-form" style="display:none;">
	<h2 text="title">Заголовок</h2>
	<div class="block"><div class="images" stormModel="MadoneShowcaseImage"></div></div>
	<div class="block"><button class="upload small-styled-button"><b><b>Загрузить изображение</b></b></button></div>
	<div class="block"><button style="margin-left:0;" class="cancel small-styled-button"><b><b>Закрыть</b></b></button></div>
</div>

<span id="image-template" class="img" style="display:none;"><div class="control"><img title="Редактировать" class="edit-image" src="icons/16/pencil.png" width="16" height="16" /><img class="delete-image" title="Удалить" src="/static/i/admin/icons/16/cross.png" width="16" height="16" /></div><img class="image" src="" /></span>
<span id="movie-template" class="img" style="display:none;"><div class="control"><img title="Редактировать" class="edit-movie" src="/static/i/admin/icons/16/pencil.png" width="16" height="16" /><img class="delete-movie" title="Удалить" src="/static/i/admin/icons/16/cross.png" width="16" height="16" /></div><img class="image" src="" /></span>

<span id="image-form" class="img-form" style="display:none;">
<img class="image" src="" />
<div class="form">
<div><label>Название изображения:</label><textarea class="width-100" name="title"></textarea></div>
<div class="block">
	<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
	<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
</div>
</div>
</span>

<span id="movie-form" class="img-form" style="display:none;">
<img class="image" src="" />
<div class="form">
<div><label>Название видео-файла:</label><textarea class="width-100" name="title"></textarea></div>
<div class="block">
	<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
	<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
</div>
</div>
</span>

<div id="movies-form" class="a-unit-form" style="display:none;">
	<h2 text="title">Заголовок</h2>
	<div class="block"><div class="movies" stormModel="MadoneShowcaseMovie"></div></div>
	<div class="block"><button class="upload small-styled-button"><b><b>Загрузить видео-файл</b></b></button></div>
	<div class="block"><button style="margin-left:0;" class="cancel small-styled-button"><b><b>Закрыть</b></b></button></div>
</div>


<?= $this->paginator ?>
</div>

<script type="text/javascript">

$( function() {

	$( '.enabled' ).click( function( e ) {
		Storm.toggle( Storm.buildPath( this ), Function.delegate( this, function( data ) {
			if(data.enabled) {
				$( this ).parents('.a-unit-body:first').removeClass('disabled');
				$( this ).attr( 'src', '/static/i/admin/icons/16/lamp-on.png');
			}
			else {
				$( this ).parents('.a-unit-body:first').addClass('disabled');			
				$( this ).attr( 'src', '/static/i/admin/icons/16/lamp-off.png' );
			}	
		} ) );
	} );
	
	$( '.delete' ).click( function( e ) {
		if( confirm( 'Вы действительно хотите удалить позицию «' + $( this ).parents( '.a-unit-body' ).find( 'h2' ).text() + '»?' ) ) {
			Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
				$( this ).parents( '.a-unit' ).remove()
			} ) );
		}
	} );
	
	var itemForm = Object.create( Storm.Form ).extend( {
		form: $( '#item-form' ),
		onFetchData: function( form, data ) {
			data.section = <?= $this->section->id ?>;
			data.price = parseInt( data.price );
			if( isNaN( data.price ) ) {
				data.price = null;
			}
		},
		onFillItem: function ( item, data ) {
			item.find( 'img.enabled' ).attr( 'src', data.enabled ? '/static/i/admin/icons/16/lamp-on.png' : '/static/i/admin/icons/16/lamp-off.png' );
			if( ! data.enabled ) {
				item.find( '.a-unit-body' ).addClass( 'disabled' );
			}
			item.attr( 'stormObject', data.id );
			item.attr( 'id', 'a-unit-' + data.id );
			item.find( 'h2:first' ).html( data.title );
			if( Madone.language !== 'ru' ) {
				item.find( 'h2:first' ).append( $( '<i class="ru-hint">(' + data.RU.title + ')</i>' ) );
			}
			item.find( 'p:first' ).html( data.price ? data.price + ' руб' : 'цена не указана' );
		}
	} );
	
	$( '.create-unit' ).click( function( e ) {
		e.preventDefault();
		Object.create( itemForm ).extend( {
			object: 'MadoneShowcaseItem',
			formPlace: $( '.createFormPlace' ),
			item: $( '#item-template' ),
			itemPlace: $( '.a-units' )
		} ).start();
	} );

	$( '.edit' ).click( function( e ) {
		Object.create( itemForm ).extend( {
			object: Storm.buildPath( this ),
			item: $( this ).parents( '.a-unit-body' )
		} ).start();
	} );
	
    // Отображение нашей непростой формы списка изображений :)
    $( '.image-list' ).bind( 'click', function( event ) {
		Object.create( Madone.ImageGallery ).extend({
			stormModel: 'MadoneShowcaseImage',
			PHPSESSID: '<?= $_COOKIE['PHPSESSID'] ?>',
			object:	Storm.buildPath( this ),
			item:	$( this ).parents( '.a-unit-body' )
		}).start();
    } );

    // Форма списка видео-файлов так же весьма непроста.
 /*
   var moviesForm = Object.create( Storm.Form ).extend( {
		form: $( '#movies-form' ),

		onCreate: function( form ) {
			// Сортировка картинок в форме
			form.find( '.movies' ).sortable( {
				start: function( e, ui ) {
					ui.item.removeClass( 'hover' );
				},
				over: function ( e, ui ) {
					ui.item.removeClass( 'hover' );
				},
				stop: function ( e, ui ) {
					var objects = {};
					form.find( '.movies .img' ).each( function( i ) {
						objects[ $( this ).attr( 'stormObject' ) ] = { position: i + 1 };
					} );
					Storm.update( 'MadoneShowcaseMovie', objects );
				}
			});

			// Загрузка картинки
			var This = this;	// В This будет ссылка на форму, чтобы из callback-ов можно было рулить
			form.find( '.upload' ).each( function () {
				var button = this;
				var uploadCount = 0;	// число выполняющихся сейчас загрузок файлов
				new AjaxUpload( this, {
						action: Storm.getPath( 'MadoneShowcaseMovie' ).getUri() + '/create/',
						name: 'movie',
						responseType: 'json',
						onSubmit : function ( file, ext ) {
							// Перед отправкой формы добавляем данные — id раздела, который берем из формы
							this.setData( { section: This.loadedData.id } );
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
			if( Madone.language !== 'ru' ) {
				form.find( 'h2:first' ).append( $( '<i class="ru-hint">(' + data.RU.title + ')</i>' ) );
			}
			this.form.find( '.movies' ).addClass( 'loading' );
			// На заполнение формы загружаем список картинок и выводим их
			var query = Object.create( Storm.Query ).use( 'filter', { section: data.id } ).use( 'order', 'position' );
			Storm.retrieve( 'MadoneShowcaseMovie', query.get(), Function.delegate( this, function ( data ) {
				this.form.find( '.movies' ).removeClass( 'loading' );
				if( Object.typeOf( data ) === 'array' && data.length ) {
					for(var i = 0; i < data.length; i++) {
						this.appendImage( data[i] );
					}
				} else {
					this.form.find( '.movies' ).removeClass( 'loading' ).addClass( 'empty' ).text( 'Видео-файлы не загружены.' );
				}
			} ) );
		},
		
		// Уникальный метод формы — добавление изображения в список, используется при отображении списка изображений
		// на сервере и добавлении загруженных изображений.
		appendImage: function ( data ) {
				// Удаляем надпись «Нет изображений», если это изображения будет первым в списке
				if( ! this.form.find( '.movies .img, .movies .img-form' ).size() ) {
					this.form.find( '.movies' ).html('').removeClass( 'empty' );
				}
				var img = $( '#movie-template' ).clone( true ).hide().removeAttr( 'id' );
				this.form.find( '.movies' ).append( img );
				img.attr( 'stormObject', data.id );
				
				var desiredWidth = 120;

				img.find( '.image' )
				.attr( 'src', data.movie ? data.movie.preview_uri : '/static/i/admin/1x1.gif' )
				.attr( 'title', data.title || '' )
				.attr( 'width', data.movie ? desiredWidth : 120 )
				.attr( 'height', data.movie ? data.movie.height * desiredWidth / data.movie.width : 120 );
				img.show();
		}
    } );
*/

    // Отображение нашей непростой формы списка изображений :)
    $( '.movie-list' ).bind( 'click', function( event ) {
		Object.create( Madone.VideoGallery ).extend( {
			stormModel: 'MadoneShowcaseMovie',
			PHPSESSID: '<?= $_COOKIE['PHPSESSID'] ?>',
			object:	Storm.buildPath( this ),
			item:	$( this ).parents( '.a-unit-body' )
		}).start();
    });

/*
	// Проявление кнопок редактирования изображения при наведении на него указателя
	$( '.img' )
		.mouseover(  function ( e ) { $( this ).addClass( 'hover' ); } )
		.mouseleave( function ( e ) { $( this ).removeClass( 'hover' ); } );

	// Удаление изображения
	$( '.delete-movie' ).click( function ( e ) {
		var img = $( this ).parents( '.img' );
		// Затеняем все изображения кроме удаляемого, чтобы дать понять пользователю, что он удаляет
		var other = $( this ).parents( '.movies' ).children().not( img );
		other.addClass( 'semitransparent' );
		if( confirm( 'Удалить видео?' ) ) {
			Storm.remove( Storm.buildPath( this ), function () {
				var list = img.parents( '.movies' );
				img.remove();
				if( ! list.find( '.img, .img-form' ).size() ) {
					list.addClass( 'empty' ).text( 'Видео-файлы не загружены.' );
				}
			} );
		}
		other.removeClass( 'semitransparent' );
	} );

	// Редактирование изображения с помощью собственной формы :D
	$( '.edit-movie' ).click( function ( e ) {
		Object.create( Storm.Form ).extend( {
			object: Storm.buildPath( this ),
			form: $( '#movie-form' ),
			item: $( this ).parents( '.img' ),
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
		} ).start();
	} );

*/

});

</script>
