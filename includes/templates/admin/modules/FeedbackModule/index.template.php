<style type="text/css">
	.a-unit-form .cancel {
		margin-left: -4px;
	}
</style>

<div class="module-content">

	<div class="a-units a-unit-list" stormModel="MadoneFeedbackMessage">
	
	<? foreach( $this->paginator->getObjects() as $i ): ?>
	<div class="a-unit">
	<div class="a-unit-body<?= $i->enabled ? '' : ' disabled'?>" stormObject="<?=$i->id?>">
		<div class="actions">
			<img title="Просмотреть" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $i->enabled ? 'on' : 'off'?>.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<h2><?= $i->name ?> <?= $i->email ?></h2>
		<p><?= $i->date ?></p>
	</div>
	</div>
	<? endforeach ?>
</div>

<div class="a-unit" id="item-template" style="display:none;">
	<div class="a-unit-body">
		<div class="actions">
			<img title="Просмотреть" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<h2>EMAIL</h2>
		<p>DATE</p>
	</div>
</div>


<div id="recipient-form" class="a-unit-form" style="display:none;">
	<div class="block"><span text="name"></span><span class="email"></span></div>
	<div class="block"><pre text="text"></pre></div>
	<div class="block" style="display:none;"><label>Текст ответа</label><textarea class="width-100 height-200" name="answer"></textarea></div>
	<div class="block">
		<button class="submit small-styled-button" style="display:none;"><b><b>Сохранить</b></b></button>		
		<button class="cancel small-styled-button"><b><b>Закрыть</b></b></button>		
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
		if( confirm( 'Вы действительно хотите удалить сообщение?' ) ) {
			Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
				$( this ).parents( '.a-unit' ).remove()
			} ) );
		}
	} );
	
	$( '.edit' ).click( function( e ) {
		Object.create( Storm.Form ).extend( {
			form: $( '#recipient-form' ),
			object: Storm.buildPath( this ),
			item: $( this ).parents( '.a-unit-body' ),
			onFill: function( form, data ) {
				if( data.email ) {
					form.find( '.email' ).html( ', <a href="mailto:{email}">{email}</a>'.supplant( data ) );
					form.find( 'textarea[name=answer]' ).parents( '.block:first').show();
					form.find( '.submit' ).show();
					form.find( '.cancel>b>b' ).text( 'Отмена' ).show();
				}
			},
			onFillItem: function( item, data ) {
				item.find( 'img.enabled' ).attr( 'src', data.enabled ?
				'/static/i/admin/icons/16/lamp-on.png' :
				'/static/i/admin/icons/16/lamp-off.png' );
				if( ! data.enabled ) {
					item.addClass( 'disabled' );
				}
			}
		} ).start();
	} )
} )

</script>
