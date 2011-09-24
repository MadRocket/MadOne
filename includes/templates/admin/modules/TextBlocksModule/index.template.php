<div class="module-buttons">
	
</div>
<div class="module-content">
<? if( $this->items ): ?>
    <div class="a-units" stormModel="MadoneTextBlock">
		<? foreach( $this->items as $i ): ?>
			<div class="a-unit">
			<div class="a-unit-body<?= $i->enabled ? '' : ' disabled'?>" stormObject="<?=$i->id?>">
				<div class="actions">
					<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
					<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $i->enabled ? 'on' : 'off'?>.png"/>
				</div>
				<h2><?=$i->name?></h2>
				<p stormHtml="preview"><?= trim($i->preview) ? $i->preview : '&nbsp;'?></p>
			</div>
			</div>
		<? endforeach ?>
    </div>

    <div id="block-form" class="a-unit-form" style="display:none;">
        <h2 text="name">Имя блока</h2>
		<div class="block"><textarea rich="yes" class="width-100 height-300" name="text"></textarea></div>
		<div class="block">
			<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
			<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
		</div>
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

		$( '.edit' ).click( function( e ) {
			Object.create( Storm.Form ).extend( {
				object: Storm.buildPath( this ),
				form: $( '#block-form' ),
				item:	$( this ).parents( '.a-unit-body' ),
				onFillItem: function( item, data ) {
					item.find( 'img.enabled' ).attr( 'src', data.enabled ?
					'/static/i/admin/icons/16/lamp-on.png' :
					'/static/i/admin/icons/16/lamp-off.png' )
				}
			} ).start();
		} )
	} )
    </script>
<? else: ?>
	<p>Ни одного текстового блока не найдено.</p>
<? endif ?>
</div>