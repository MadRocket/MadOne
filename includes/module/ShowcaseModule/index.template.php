<?
    $root = $this->items[0];
    $this->items = $this->items[0]->getChildren();
?>

<div class="module-buttons">
	<button class="create-section styled-button"><b><b>Создать раздел</b></b></button>
</div>

<div class="module-content">
	
	<div class="createFormPlace"></div>

	<div class="a-units" stormModel="MadoneShowcaseSection">
	<span id="sections" class="a-unit-list">
		<?foreach( $this->items as $i ):?>
			<? printItem( $i, $this ) ?>
		<?endforeach?>
	</span>
	</div>
</div>

<? function printItem( $p, $template ) { ?>
    <div class="a-unit" stormObject="<?=$p->id?>" id="a-unit-<?=$p->id?>">
            <div class="a-unit-body<?= $p->enabled ? '' : ' disabled'?>">
                <div class="actions">
					<a class="items" href="./<?= $p->id ?>/" title="Перейти к списку позиций"><img class="item-list" width="16" height="16" src="/static/i/admin/icons/16/items.png"/></a>
                    <img class="edit" title="Редактировать" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
					<img class="enabled" title="Включить/выключить" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $p->enabled ? 'on' : 'off'?>.png"/>
					<img class="delete" title="Удалить" width="16" height="16" alt="Удалить" src="/static/i/admin/icons/16/cross.png"/>
                </div>
                <h2><?= $p->title ? $p->title : "&nbsp;" ?><? if( ! Madone::isCurrentLanguage( 'ru' ) && $p->RU->title ) :?><i class="ru-hint">(<?= $p->RU->title ?>)</i><? endif ?></h2>
                <p><?=$p->name?></p>
            </div>
            <?if( count( $p->getChildren() ) ):?>
                <span class="a-unit-list">
                <?foreach( $p->getChildren() as $c ):?>
                    <? printItem( $c, $template ) ?>
                <?endforeach?>
                </span>
            <?endif?>
    </div>
<? } ?>

<div id="section-form" class="a-unit-form" style="display:none;">
	<div class="block"><label>Название:</label><input class="width-100" type="text" name="title"/></div>
	<div class="block"><label>Название по-английски:</label><input class="width-100" type="text" name="name"/></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>

<div class="a-unit" id="section-template" style="display:none;">
    <div class="a-unit-body">
        <div class="actions">
			<a class="items" href="#" title="Перейти к списку позиций"><img class="item-list" width="16" height="16" src="/static/i/admin/icons/16/items.png"/></a>
			<img class="edit" title="Редактировать" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img class="enabled" title="Включить/выключить" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img class="delete" title="Удалить" width="16" height="16" alt="Удалить" src="/static/i/admin/icons/16/cross.png"/>
        </div>
        <h2>Название раздела</h2>
        <p>name</p>
    </div>
</div>

<script>

$( function () {
	// Инициализация (и переинициализация) сортировки дерева разделов
	function initSortable() {
		$( '#sections' ).NestedSortableDestroy().NestedSortable( Object.create( Madone.NestedSortableOptions ).extend( {
			onChange: function ( serialized ) {
				Storm.reorder( 'MadoneShowcaseSection', { id: <?=$root->id?>, children: serialized[0].o.sections } );
			}
		} ) );
		$( '.a-unit-body:not( .root )' ).addClass( 'movable' );
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
		} ) );
	} );

	// Удаление раздела
	$( '.delete' ).click( function( e ) {
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
			} ) );
		}
	} );
	
	var sectionForm = Object.create( Storm.Form ).extend( {
		form: $( '#section-form' ),
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
			item.find( 'a.items:first' ).attr( 'href', './' + data.id + '/' );
		},
		onShowItem: initSortable
	} );

    // Создание раздела
    $( '.create-section' ).click( function ( e ) {
        e.preventDefault();
		Object.create( sectionForm ).extend( {
			object:		'MadoneShowcaseSection',
			formPlace:	$( '.createFormPlace' ),
			item:		$( '#section-template' ),
			itemPlace:	$( '#sections' )
		} ).start();
	} );

    // Редактирование раздела
    $( '.edit' ).bind( 'click', function( event ) {
		event.preventDefault();
		Object.create( sectionForm ).extend( {
			object:	Storm.buildPath( this ),
			item:	$( this ).parents( '.a-unit-body' )
		} ).start();
    } );
    
} );


</script>
