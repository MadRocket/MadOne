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

<?
    $root = $this->items[0];
    $this->items = $this->items[0]->getChildren();
?>

<div class="module-buttons">
	<button class="create-section styled-button"><b><b>Создать раздел</b></b></button>
</div>

<div class="module-content">
	
	<div class="createFormPlace"></div>
	
	<div class="a-units" stormModel="MadoneGallerySection">
	<span id="sections" class="a-unit-list">
	    <? foreach( $this->items as $i ): ?>
	        <? // printItem( $i, $this ) ?>
	    <? endforeach ?>
	</span>
	</div>
</div>
<? function printItem( $p, $template ) { ?>
    <div class="a-unit" stormObject="<?=$p->id?>" id="a-unit-<?=$p->id?>">
            <div class="a-unit-body<?= $p->enabled ? '' : ' disabled'?>">
                <div class="actions">
					<img class="image-list" title="Открыть список изображений" width="16" height="16" src="/static/i/admin/icons/16/image.png"/>
                    <img class="edit" title="Редактировать" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
					<img class="enabled" title="Включить/выключить" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $p->enabled ? 'on' : 'off'?>.png"/>
					<img class="delete" title="Удалить" width="16" height="16" alt="Удалить" src="/static/i/admin/icons/16/cross.png"/>
                </div>
                <h2><?=$p->title?></h2>
                <p><?=$p->name?></p>
            </div>
            <? if( count( $p->getChildren() ) ):?>
                <span class="a-unit-list">
                <? foreach( $p->getChildren() as $c ):?>
                    <? printItem( $c, $template ) ?>
                <? endforeach?>
                </span>
            <? endif ?>
    </div>
<? } ?>

<div id="section-form" class="a-unit-form" style="display:none;">
	<div class="block"><label>Название раздела</label><input class="width-100" type="text" name="title"/></div>
	<div class="block"><label>Название по-английски</label><input class="width-100" type="text" name="name"/></div>
	<div class="block"><label>Описание</label><textarea class="width-100 height-300" rich="yes" name="text"></textarea></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>

<div id="images-form" class="a-unit-form" style="display:none;">
	<h2 text="title">Заголовок</h2>
	<div class="block"><div class="images" stormModel="MadoneGalleryImage"></div></div>
	<div class="block">
		<button class="upload small-styled-button"><b><b>Загрузить изображение</b></b></button>
	</div>
	<div class="block">
		<button style="margin-left:0;" class="cancel small-styled-button"><b><b>Закрыть</b></b></button>
	</div>
</div>

<div class="a-unit" id="section-template" style="display:none;">
    <div class="a-unit-body">
        <div class="actions">
			<img class="image-list" title="Открыть список изображений" width="16" height="16" src="/static/i/admin/icons/16/image.png"/>
			<img class="edit" title="Редактировать" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img class="enabled" title="Включить/выключить" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img class="delete" title="Удалить" width="16" height="16" alt="Удалить" src="/static/i/admin/icons/16/cross.png"/>
        </div>
        <h2 stormGrid="title">Название раздела</h2>
        <p stormGrid="description">name</p>
    </div>
</div>

<span id="image-template" class="img" style="display:none;"><div class="control"><img title="Редактировать" class="edit" src="/static/i/admin/icons/16/pencil.png" width="16" height="16" /><img class="delete" title="Удалить" src="/static/i/admin/icons/16/cross.png" width="16" height="16" /></div><img class="image" src="" /></span>

<span id="image-form" class="img-form" style="display:none;">
<img class="image" src=""><div class="form">
<div><label>Название изображения</label><textarea class="width-100" name="title"></textarea></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>
</span>

<script>
$( function () {

	var sectionForm = Object.create( Storm.Form ).extend( {
		form: $( '#section-form' )
	});
	
	// Форма списка картинок имеет собственную непростую функциональность.
	// Поэтому создаем объект на прототипе Storm.Form, расширяем его, и формы картинок будем порождать
	// от этой расширенной формы, а не от стандартной.
	var itemsForm = Object.create( Storm.Form ).extend( {
		form: $( '#images-form' ),
		
		onCreate: function( form ) {
			// Загрузка картинки
			var This = this;	// В This будет ссылка на форму, чтобы из callback-ов можно было рулить
			form.find( '.upload' ).each( function () {
				var button = this;
				var uploadCount = 0;	// число выполняющихся сейчас загрузок файлов
				new AjaxUpload( this, {
					action: Storm.getPath( 'MadoneGalleryImage' ).getUri() + '/create/',
					name: 'image',
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
							This.imagesGrid.addItem( r.data );
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
			var query = Object.create( Storm.Query ).use( 'filter', { section: data.id } ).use( 'order', 'position' );
			Storm.retrieve( 'MadoneGalleryImage', query.get(), Function.delegate( this, function ( data ) {
				this.form.find( '.images' ).removeClass( 'loading' );
				
				// Создадим grid, который будет рулить картинками
				this.imagesGrid = Object.create(Storm.Grid).extend({
					model: 'MadoneGalleryImage',
					place: this.form.find('.images'),
					item: $('#image-template'),
					itemForm: Object.create( Storm.Form ).extend({
						form: $( '#image-form' )
					}),
					items: data,
					fillItem: function( item, data ) {
						item.find('.image')
						.attr( 'src', data.image.cms ? data.image.cms.uri : Madone.uri + '/static/i/1x1.gif' )
						.attr( 'title', data.title || '' )
						.attr( 'width', data.image.cms ? data.image.cms.width : 50 )
						.attr( 'height', data.image.cms ? data.image.cms.height : 50 );

						return item;
					},
					emptyText: "В данном разделе еще нет изображений",
					simpleSortable: true,
					editHandler: function(e) {
						Object.create( Storm.Form ).extend( {
							object: Storm.buildPath( this ),
							item: $( this ).parents( '.img' ),
							onFill: function ( form, data ) {
								form.find( '.image' )
								.attr( 'src', data.image.cms ? data.image.cms.uri : Madone.uri+'/static/i/1x1.gif' )
								.attr( 'width', data.image.cms ? data.image.cms.width : 50 )
								.attr( 'height', data.image.cms ? data.image.cms.height : 50 );
							},
							onFillItem: function( item, data ) {
								item.find( '.image' ).attr( 'title', data.title || '' );
							}
						}).start();
					},
					deleteHandler: function(e) {
						var img = $( this ).parents( '.stormGridItem:first' );
						
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
							});
						}
						other.removeClass( 'semitransparent' );
					}
				}).start();
			}));
		}
    });
    
    
	// Проявление кнопок редактирования изображения при наведении на него указателя
	$( '.img' )
		.mouseover(  function ( e ) { $( this ).addClass( 'hover' ); } )
		.mouseleave( function ( e ) { $( this ).removeClass( 'hover' ); } );

	var grid = Object.create(Storm.Grid).extend({
		model: 'MadoneGallerySection',
		place: $('#sections'),
		item: $("#section-template"),
		items: <?= json_encode(Mad::getJsonSafe($this->items)) ?>,
		itemForm: sectionForm,
		itemDataMapper: {
			'title': function(data) {
				return data.title ? data.title : "&nbsp;";
			},
			'description': 'name'
		},
		nestedSortable: { rootID: <?=$root->id?>, movable: '.a-unit-body' },
		editHandler: function(e) {
			Object.create( sectionForm ).extend( {
				object:	Storm.buildPath( this ),
				item:	$( this ).parents( '.a-unit-body' )
			}).start();
		},
		deleteHandler: function(e) {
			var title = $( this ).parents( '.a-unit:first' ).find( 'h2:first' ).text();
	        if( confirm( 'Вы действительно хотите удалить раздел «' + title  + '»?' ) ) {
				var nested = $( this ).parents( '.a-unit:first' ).find( '.a-unit' ).size();
	            if( nested ) {
	                if( ! confirm( Mad.decline( nested, '',
						'«' + title +'» содержит еще %n вложенный раздел.',
						'«' + title +'» содержит еще %n вложенных раздела.',
						'«' + title +'» содержит еще %n вложенных разделов.' ) +
						' Вы действительно хотите удалить их все?' ) )
	                return false;
	            }
				Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
					$( this ).parents( '.a-unit:first' ).remove();
				}));
			}
		},
		customHandlers: {
			'.image-list': function( e ) {
				// Отображаем форму добавления картинок
				
				Object.create( itemsForm ).extend( {
					object:	Storm.buildPath( this ),
					item:	$( this ).parents( '.a-unit-body' )
				}).start();
		    }
		}
	});
		
	grid.start();


    // Создание раздела
    $( '.create-section' ).click( function ( e ) {
		Object.create( sectionForm ).extend( {
			object:		'MadoneGallerySection',
			formPlace:	$( '.createFormPlace' ),
			item:		$( '#section-template' ),
			itemPlace:	$( '#sections' )
		}).start();
	});	
});

</script>
