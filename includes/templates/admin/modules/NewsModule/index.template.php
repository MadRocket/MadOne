<div class="module-buttons">
	<button class="create-unit styled-button"><b><b>Добавить новость</b></b></button>	
</div>

<div class="module-content">
	<div class="createFormPlace"></div>
	
	<div class="a-units a-unit-list" stormModel="MadoneNews">
	
	<? foreach( $this->paginator->getObjects() as $i ): ?>
	<div class="a-unit">
	<div class="a-unit-body<?= $i->enabled ? '' : ' disabled'?>" stormObject="<?=$i->id?>">
		<div class="actions">
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $i->enabled ? 'on' : 'off'?>.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<h2><?= $i->title ? $i->title : "&nbsp;" ?><? if( ! Madone::isCurrentLanguage( 'ru' ) && $i->RU->title ) :?><i class="ru-hint">(<?= $i->RU->title ?>)</i><? endif ?></h2>
		<p><?=$i->date?></p>
	</div>
	</div>
	<? endforeach ?>
</div>

<div id="news-form" class="a-unit-form" style="display:none;">
	<div class="block"><label>Заголовок новости:</label><input class="width-100" type="text" name="title"/></div>
	<div class="block"><label>Дата публикации:</label><input datepicker="yes" class="width-100" type="text" name="date"/></div>
	<div class="block"><label>Анонс:</label><textarea rich="no" class="width-100 height-200" name="announce"></textarea></div>
	<div class="block"><textarea rich="yes" class="width-100 height-300" name="text"></textarea></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>		
	</div>
</div>

<?= $this->paginator ?>
</div>

<script type="text/javascript">

$( function() {
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
	
	$( '.delete' ).click( function( e ) {
		if( confirm( 'Вы действительно хотите удалить новость «' + $( this ).parents( '.a-unit-body' ).find( 'h2' ).text() + '»?' ) ) {
			Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
				$( this ).parents( '.a-unit' ).remove()
				location.reload()
			} ) );
		}
	} );
	
	var newsForm = Object.create( Storm.Form ).extend( {
		form: $( '#news-form' ),
		onSubmit: function( form, data, result ) {
			if( this.mode === 'create' || this.loadedData.date != result.date ) {
				location.reload();
			}
		}
	} );
	
	var createFormOpened = false
	$( '.create-unit' ).click( function( e ) {
		e.preventDefault();
		Object.create( newsForm ).extend( {
			object: 'MadoneNews',
			formPlace: $( '.createFormPlace' )
		} ).start();
	} );

	$( '.edit' ).click( function( e ) {
		Object.create( newsForm ).extend( {
			object: Storm.buildPath( this ),
			item: $( this ).parents( '.a-unit-body' ),
			onFillItem: function( item, data ) {
				item.find( 'img.enabled' ).attr( 'src', data.enabled ?
				'/static/i/admin/icons/16/lamp-on.png' :
				'/static/i/admin/icons/16/lamp-off.png' );
				if( ! data.enabled ) {
					item.find( '.a-unit-body' ).addClass( 'disabled' );
				}
				item.attr( 'stormObject', data.id );
				item.find( 'h2:first' ).html( data.title );
				if( Madone.language !== 'ru' ) {
					item.find( 'h2:first' ).append( $( '<i class="ru-hint">(' + data.RU.title + ')</i>' ) );
				}
				item.find( 'p:first' ).html( data.date );
			}
		} ).start();
	} )
} )

</script>