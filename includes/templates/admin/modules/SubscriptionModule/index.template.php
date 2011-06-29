<style type="text/css">
	.a-unit-form .attach {
		margin-left: -4px;
	}
	.remove-attachment {
		vertical-align: middle;
		margin-left:10px;
		cursor: pointer;
	}
</style>

<div class="module-buttons">
	<button class="mail styled-button"><b><b>Создать рассылку</b></b></button>
	<button class="create-unit styled-button"><b><b>Добавить адрес в список рассылки</b></b></button>
</div>

<div class="module-content">

	<form id="mail-form" style="display:none;" class="a-unit-form" action="<?= $this->ajaxUri ?>" method="POST">
		<div class="block"><label>Тема письма:</label><input class="width-100" type="text" name="title"/></div>
		<div class="block"><label>Текст письма:</label><textarea class="width-100 height-200" name="text"></textarea></div>
		<div class="block attachments" stormModel="MadoneTempFile"></div>
		<div class="block">
			<button class="attach small-styled-button" type="button"><b><b>Прикрепить файл</b></b></button>
		</div>
		<div class="block">
			<button class="submit small-styled-button" type="submit"><b><b>Выполнить рассылку</b></b></button>
			<button class="cancel small-styled-button" type="button"><b><b>Отмена</b></b></button>		
		</div>
	</form>

	<div class="attachment-template attachment-form" style="display:none;"><a class="attachment" target="_blank" href="/">filename.txt</a><input type="hidden" name="attachments[]" value=""/><img title="Удалить прикрепленный файл" class="remove-attachment" src="/static/i/admin/icons/16/cross.png?fff" /></div>

	<div class="createFormPlace"></div>
	
	<div class="a-units a-unit-list" stormModel="MadoneSubscriptionRecipient">
	
	<? foreach( $this->paginator->objects as $i ): ?>
	<div class="a-unit">
	<div class="a-unit-body<?= $i->enabled ? '' : ' disabled'?>" stormObject="<?=$i->id?>">
		<div class="actions">
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $i->enabled ? 'on' : 'off'?>.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<h2><?= $i->email ?></h2>
		<p><?= $i->date ?></p>
	</div>
	</div>
	<? endforeach ?>
</div>

<div id="recipient-form" class="a-unit-form" style="display:none;">
	<div class="block"><label>Адрес электронной почты:</label><input class="width-100" type="text" name="email"/></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>		
	</div>
</div>

<div class="a-unit" id="item-template" style="display:none;">
	<div class="a-unit-body">
		<div class="actions">
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<h2>EMAIL</h2>
		<p>DATE</p>
	</div>
</div>


<div id="recipient-form" class="a-unit-form" style="display:none;">
	<div class="block"><label>Адрес электронной почты:</label><input class="width-100" type="text" name="email"/></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>		
	</div>
</div>

<?= $this->paginator ?>
</div>

<script type="text/javascript">

$( function() {
	$( '.mail' ).click( function( event ) {
		$('#mail-form').show();
	} );
	
	$('#mail-form .cancel').click( function( event ) {
		$('#mail-form').hide().clearForm().find( '.attachment-form' ).each( function() {
			Storm.remove( Storm.buildPath( this ) );
		} ).remove();
	} );

	var uploadCount = 0;	// число выполняющихся сейчас загрузок файлов
	
	$('#mail-form').ajaxForm({ 
		dataType:  'json',
		beforeSubmit: function( data, form, options ) {
			if( uploadCount ) {
				alert( 'Пожалуйста, дождитесь окончания загрузки прикрепленного файла.' );
				return false;
			}
			for( var i = 0; i < data.length; i++ ) {
				var input = data[ i ];
				if( ! input.value ) {
					switch( input.name ) {
					case 'title':
						alert( 'Пожалуйста, укажите тему письма.' );
						break;
					case 'text':
						alert( 'Пожалуйста, укажите текст письма.' );
						break;
					}
					return false;
				}
			}
			return true;
		},
		success: function( r ) {
			if( r.success ) {
				$('#mail-form').hide().clearForm().find( '.attachment-form' ).remove();
				alert( 'Сообщение успешно отправлено.' );
			} else {
				alert( 'Не удалось выполнить рассылку. '  + r.message );
			}
		}
	});
	
	$( '.remove-attachment' ).click( function( event ) {
		Storm.remove( Storm.buildPath( this ) );
		$( this ).parents( 'div:first' ).remove();
	} );
	
	new AjaxUpload( $('#mail-form .attach'), {
		action: Storm.getPath( 'MadoneTempFile' ).getUri() + '/create/',
		name: 'file',
		responseType: 'json',
		onSubmit : function ( file, ext ) {
			Madone.addAjaxLoader( $('#mail-form .attach') );
			uploadCount++;
		},
		onComplete: function ( file, r ) {
			Madone.removeAjaxLoader( $('#mail-form .attach') );
			if( r.success ) {
				$( '.attachment-template' ).clone(true).removeClass( 'attachment-template' )
				.appendTo( '.attachments' )
				.attr( 'stormObject', r.data.id )
				.find( 'input[name=attachments\[\]]' ).val( r.data.id + ':' + file ).end()
				.find( '.attachment' ).text( file ).attr( 'href', r.data.file.uri ).end()
				.show();
				uploadCount--;
			}
			else {
				alert( 'Ошибка при загрузке файла ' + file + '. ' + r.message );
			}
		}
	});

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
		if( confirm( 'Вы действительно хотите удалить адрес «' + $( this ).parents( '.a-unit-body' ).find( 'h2' ).text() + '»?' ) ) {
			Storm.remove( Storm.buildPath( this ), Function.delegate( this, function() {
				$( this ).parents( '.a-unit' ).remove()
			} ) );
		}
	} );
	
	var recipientForm = Object.create( Storm.Form ).extend( {
		form: $( '#recipient-form' ),
		onFillItem: function( item, data ) {
			item.find( 'img.enabled' ).attr( 'src', data.enabled ?
			'/static/i/admin/icons/16/lamp-on.png' :
			'/static/i/admin/icons/16/lamp-off.png' );
			if( ! data.enabled ) {
				item.find( '.a-unit-body' ).addClass( 'disabled' );
			}
			item.attr( 'stormObject', data.id );
			item.find( 'h2:first' ).html( data.email );
			item.find( 'p:first' ).html( data.date );
		}
	} );
	
	var createFormOpened = false
	$( '.create-unit' ).click( function( e ) {
		e.preventDefault();
		Object.create( recipientForm ).extend( {
			object: 'MadoneSubscriptionRecipient',
			formPlace: $( '.createFormPlace' ),
			item: $( '#item-template' ),
			itemPlace: $( '.a-units' )
		} ).start();
	} );

	$( '.edit' ).click( function( e ) {
		Object.create( recipientForm ).extend( {
			object: Storm.buildPath( this ),
			item: $( this ).parents( '.a-unit-body' )
		} ).start();
	} )
} )

</script>
