<div class="module-buttons">
	<button class="create-page styled-button"><b><b>Создать страницу</b></b></button>
</div>
<div id="quick-help">
	<ul>
		<li><img src="/static/i/admin/icons/16/pencil.png"> Редактирование страницы</li>
		<li><img src="/static/i/admin/icons/16/lamp-on.png"> Страница включена — выключить</li>
		<li><img src="/static/i/admin/icons/16/lamp-off.png"> Страница выключена — включить</li>
		<li><img src="/static/i/admin/icons/16/flag-on.png"> Страница отображается в меню — спрятать</li>
		<li><img src="/static/i/admin/icons/16/flag-off.png"> Страница не отображается в меню — показать</li>
		<li><img src="/static/i/admin/icons/16/cross.png"> Удалить страницу</li>
	</ul>
</div>
<div class="module-content">
<?
    $root = $this->items[0];
    $this->items = $this->items[0]->getChildren();
?>

<div class="createFormPlace"></div>

<div class="a-units" stormModel="MadonePage">

<div class="a-unit" stormObject="<?=$root->id?>" id="a-unit-<?=$root->id?>">
	<div class="a-unit-body root">
        <div class="actions">
			<img width="16" class="edit" title="Редактировать" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img width="16" height="16" class="noclick" src="/static/i/admin/1x1.gif"/>
			<img width="16" height="16" class="noclick" src="/static/i/admin/1x1.gif"/>
			<img width="16" height="16" class="noclick" src="/static/i/admin/1x1.gif"/>						
        </div>
        <h2><?= $root->title ? $root->title : "&nbsp;" ?><? if( ! Madone::isCurrentLanguage( 'ru' ) && $root->RU->title ) :?><i class="ru-hint">(<?= $root->RU->title ?>)</i><? endif ?></h2>
        <p><?= $root->name ? $root->name : "&nbsp;" ?></p>
    </div>
</div>

<span id="pages" class="a-unit-list">
    <? foreach( $this->items as $i ): ?>
        <? printItem( $i, $this ) ?>
    <? endforeach ?>
</span>
</div>

<? function printItem( $p, $template ) { ?>
    <div class="a-unit" stormObject="<?=$p->id?>" id="a-unit-<?=$p->id?>">
        <div class="a-unit-body<?= $p->enabled ? '' : ' disabled'?>">
            <div class="actions">
                <img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
				<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $p->enabled ? 'on' : 'off'?>.png"/>
				<img title="Показывать в меню" class="menu" stormField="menu" width="16" height="16" src="/static/i/admin/icons/16/flag-<?= $p->menu ? 'on' : 'off'?>.png"/>
				<img title="Удалить" class="delete" width="16" height="16" alt="Удалить" src="/static/i/admin/icons/16/cross.png"/>
            </div>
            <h2><?= $p->title ? $p->title : "&nbsp;" ?><? if( ! Madone::isCurrentLanguage( 'ru' ) && $p->RU->title ) :?><i class="ru-hint">(<?= $p->RU->title ?>)</i><? endif ?></h2>
            <p><?= $p->name ? $p->name : "&nbsp;" ?></p>
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
</div>
<!-- форма редактирования страницы -->
<div id="page-form" class="a-unit-form" style="display:none;">
    <div class="block"><label>Название страницы:</label><input class="width-100" type="text" name="title"/></div>

    <div class="block"><label>Название по-английски:</label><input class="width-100" type="text" name="name"/></div>
    
    <div class="block">
	    <label>Тип страницы:</label>
		<select name="type">
		<? foreach( $this->types as $type ): ?>
			<option value="<?=$type->id?>"><?=$type->title?></option>
		<? endforeach ?>
		</select>
		<select name="app_settings">
		</select>
    </div>

	<div class="block text-block" style="display:block;">
        <label>Текст страницы:</label>
        <textarea name="text" class="width-100 height-300" rich="yes"></textarea>
    </div>

    <div class="block meta-block" style="display:none;">
        <a class="ajax" extra="metaVisible" href="">Ключевые слова, описание и заголовок (SEO)</a>
        <div style="display:none;">
            <label>Заголовок:</label>
            <input class="width-100" name="meta_title"/>
            <div class="block">
            <label>Ключевые слова:</label>
            <textarea class="width-100" name="meta_keywords" wrap="wrap"></textarea>
            </div>
            <div class="block">
            <label>Описание:</label>
            <textarea class="width-100" name="meta_description" wrap="wrap"></textarea>
            </div>
        </div>
    </div>
	
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>		
	</div>
</div>
<!-- /форма редактирования страницы -->

<!-- шаблон страницы в дереве -->

<div class="a-unit" id="page-template" style="display:none;">
	<div class="a-unit-body">
        <div class="actions">
            <img class="edit "width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
            <img class="menu" stormField="menu" width="16" height="16" src="/static/i/admin/icons/16/flag-on.png"/>
            <img class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
        </div>
        <h2>Заголовок страницы</h2>
        <p>page_name</p>
    </div>
</div>

<!--/ шаблон страницы в дереве -->

<script type="text/javascript">
$( function () {
	// Сортировка страниц перетаскиванием
	function initSortable() {
		$('#pages').NestedSortableDestroy().NestedSortable( Object.create( Madone.NestedSortableOptions ).extend( {
			onChange: function ( serialized ) {
				Storm.reorder( 'MadonePage', { id: <?=$root->id?>, children: serialized[0].o.pages } );
				findFirstLast('.a-units .a-unit-body');
			}
		}));
		
		$( '.a-unit-body:not( .root )' ).addClass( 'movable' );
	}
	initSortable();

	<?
		$types = array();
		foreach( $this->types as $type ) {
			$types[ $type->id ] = $type->asArray( true );
			$types[ $type->id ]['settings'] = json_decode($type->settings);
		}
	?>
	
	var pageForm = Object.create( Storm.Form ).extend( {
		form: $( '#page-form' ),
				
		onCreate: function () {
			this.metaVisible = false;
		},
		
		onFill: function ( form, data ) {
			form.find( '.block > a' ).bind( 'click', { form: this }, function ( e ) {
				e.preventDefault();
				if( $( this ).attr( 'extra' ) ) {
					e.data.form[ $( this ).attr( 'extra' ) ] = true;
				}
				$( this ).next( 'div' ).show().end().remove();
			} );
		
			form.find( 'select[name=type]' ).bind( 'change', function ( e ) {
				// Если тип не указан выбираем 1 - обычная страница
				if(! $(this).val()) {
					$(this).val(1);
				}
	
				var types = <?= json_encode( $types ) ?>;
				form.find( '.text-block' ).toggle( !! types[ $(this).val() ].has_text );
				form.find( '.meta-block' ).toggle( !! types[ $(this).val() ].has_meta );
				
				// Заполним settings
				var settings = types[ $(this).val() ].settings;
								
				// Для данного типа страницы определены настройки
				if( settings ) {
					var settingsEl = form.find('select[name=app_settings]');
					settingsEl.show();
					settingsEl.attr('enabled', 'disabled');
					var This = this;
					
					// Получим данные модели указаной в настройках
					Storm.retrieve( settings.model,  Object.create( Storm.Query ).use( 'order', 'id' ), function ( settingsData ) {
						settingsEl.attr('enabled', 'enabled');
						settingsEl.find('option').remove();
						settingsEl.append($('<option>').attr('value', 'null').text( '---' ) );
						for(var item in settingsData) {
							var option = $('<option>').attr('value', settingsData[item].id).text( settingsData[item].title );
							if( settingsData[item].id == data.app_settings) {
								option.attr('selected', 'selected');
							}
							
							settingsEl.append(option);
						}
					});
				} else {
					form.find('select[name=app_settings]').hide();
				}
			} ).trigger( 'change' );
		},

		onFetchData: function ( form, data ) {
			if( ! this.metaVisible ) {
				delete data.meta_title;
				delete data.meta_keywords;
				delete data.meta_description;
			}
			
			if(data.app_settings == 'null') {
				data.app_settings = null;
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
			item.find( 'p:first' ).html( data.name );
		},
		
		onShowItem: function () {
			initSortable();
		}
	} );
	
    // Новая страница
    $(".create-page").click( function ( e ) {
        e.preventDefault();
		Object.create( pageForm ).extend( {
			object:		'MadonePage',
			formPlace:	$( '.createFormPlace' ),
			item:		$( '#page-template' ),
			itemPlace:	$( '#pages' )
		} ).start();
	} );
    
    // Редактирование страницы
    $( '.edit' ).bind( 'click', function( event ) {
		event.preventDefault();
		Object.create( pageForm ).extend( {
			object:	Storm.buildPath( this ),
			item:	$( this ).parents( '.a-unit-body' )
		} ).start();
    } );

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
	} );

	$( '.menu' ).click( function( e ) {
		Storm.toggle( Storm.buildPath( this ), Function.delegate( this, function ( data ) {
			$( this ).attr( 'src', data.menu ?
			'/static/i/admin/icons/16/flag-on.png' :
			'/static/i/admin/icons/16/flag-off.png' )
		}));
	});
	
	$( '.delete' ).click( function( e ) {
		var title = $( this ).parents( '.a-unit:first' ).find( 'h2:first' ).text();

        if( confirm( 'Вы действительно хотите удалить страницу «' + title  + '»?' ) ) {
			var nested = $( this ).parents( '.a-unit:first' ).find( '.a-unit' ).size();
            if( nested ) {
                if( ! confirm( Mad.decline( nested, '',
					'«' + title +'» содержит еще %n вложеную страницу.',
					'«' + title +'» содержит еще %n вложеные страницы.',
					'«' + title +'» содержит еще %n вложеных страниц.' ) +
					' Вы действительно хотите удалить их все?' ) )
                return false;
            }
            
			Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
				$( this ).parents( '.a-unit:first' ).remove();
			}));
		}
	});
});
</script>
