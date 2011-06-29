<style>
.messages div {
	display: inline-block;
	~display: inline;
	width: 100%;
	margin: 5px 0 0 0;
	
    padding: 7px 5px;
    background: #e3c67f;
    border: 1px solid #be7e22;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
}

.messages .fix {
	font-size: 12px;
	padding-top: 5px;
	
}

.installed.a-units {
	cursor: move;
}

.found-apps {
    margin-bottom: 15px;
}

h3 {
	margin-bottom: 10px;
}
.found-apps .actions {
	margin: 0;
}
.ui-sortable-placeholder {
	padding-bottom: 1px;
	margin-bottom: -1px;	
}
.ui-sortable-helper {

}
</style>

<? if(count($this->messages) == 0): ?>
<div class="module-buttons">
	<button class="MadonePageType-create styled-button"><b><b>Создать приложение</b></b></button>
</div>

<div class="module-content">

<div class="createFormPlace"></div>

<? if(count($this->newapps) > 0): ?>
<h3>В системе найдены неустановленные приложения<br/><small>Некоторые приложения связаны с определенными модулями, раздельная установка может привести к провалу</small></h3>
<div class="a-units found-apps" stormModel="MadonePageType">
	<? foreach( $this->newapps as $newapp ): ?>
		<div class="a-unit" has_text="<?= $newapp['has_text'] ?>" has_meta="<?= $newapp['has_meta'] ?>" has_subpages="<?= $newapp['has_subpages'] ?>" priority="<?= $newapp['priority'] ?>">
		<div class="a-unit-body">
			<div class="actions">
				<img title="Установить" class="install" width="16" height="16" src="/static/i/admin/icons/16/plus.png"/>
				<img title="Удалить" class="remove" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
			</div>
			<div class="title"><?= $newapp['title'] ?></div>
			<p class="app_classname"><?= $newapp['app_classname'] ?></p>
		</div>
		</div>
	<? endforeach ?>
</div>
<? endif ?>

<h3>Установленные приложения</h3>
<div class="installed a-units" stormModel="MadonePageType">
	<? foreach( $this->apps as $m ): ?>
		<div class="a-unit" id="block-<?=$m->id?>" blockId="<?=$m->id?>">
		<div class="a-unit-body<?= $m->enabled ? '' : ' disabled'?>" stormObject="<?=$m->id?>">
			<div class="actions">
				<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
				<img title="Продублировать" class="duplicate" width="16" height="16" src="/static/i/admin/icons/16/copy.png"/>
				<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $m->enabled ? 'on' : 'off'?>.png"/>
				<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
			</div>
			<div class="title"><?=$m->title?></div>
			<p stormHtml="name"><?= $m->app_classname ?></p>
		</div>
		</div>
	<? endforeach ?>
</div>
<? endif ?>

<div class="messages">
	<? foreach($this->messages as $message): ?>
		<div>
			<p><?= $message['message'] ?></p>
			<p class="fix"><?= $message['fix'] ?></p>
		</div>
	<? endforeach ?>
</div>
</div>

<div id="MadonePageType-item" class="a-unit" style="display: none;">
	<div class="a-unit-body" stormObject="">
		<div class="actions">
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Продублировать" class="duplicate" width="16" height="16" src="/static/i/admin/icons/16/copy.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<div stormHtml="title" class="title"></div>
		<p stormHtml="app_classname" class="app_classname"></p>
	</div>
</div>

<div id="MadonePageType-form" class="a-unit-form" style="display: none;">
	<div class="block"><label>Название приложения:</label><input name="title" type="text" class="width-100" /></div>
	<div class="block"><label>Имя класса:</label><input name="app_classname" type="text" class="width-100" /></div>
	<div class="block"><label><input name="has_text" type="checkbox" /> &mdash; может иметь текст</label></div>
	<div class="block"><label><input name="has_meta" type="checkbox" /> &mdash; может иметь метаданные (метатеги description, keywords)</label></div>
	<div class="block"><label><input name="has_subpages" type="checkbox" /> &mdash; может иметь дочерние страницы</label></div>
	<div class="block"><label>Приоритет:</label><input name="priority" type="text" class="width-100" /></div>

	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>

<div id="MadonePageType-install-form" class="a-unit-form" style="display: none;">	
	<div class="block"><label>Название приложения:</label><input name="title" type="text" class="width-100" /></div>
	<div class="block"><label>Имя класса:</label><input name="app_classname" type="text" class="width-100" /></div>
	<div class="block"><label><input name="has_text" type="checkbox" /> &mdash; может иметь текст</label></div>
	<div class="block"><label><input name="has_meta" type="checkbox" /> &mdash; может иметь метаданные (метатеги description, keywords)</label></div>
	<div class="block"><label><input name="has_subpages" type="checkbox" /> &mdash; может иметь дочерние страницы</label></div>
	<div class="block"><label>Приоритет:</label><input name="priority" type="text" class="width-100" /></div>

	<div class="block">
		<button class="submit small-styled-button"><b><b>Установить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>		
	</div>
</div>


<script type="text/javascript">
$( function() {
	/**
	 * Установленные приложения
	 */

	$( '.MadonePageType-create' ).click( function( e ) {
		Object.create( Storm.Form ).extend( {
			form: $( '#MadonePageType-form' ),
			object: 'MadonePageType',
			formPlace: $( '.createFormPlace' ),
			item:		$( '#MadonePageType-item' ),
			itemPlace:	$( '.installed.a-units' ),
			onSubmit: function( form, data, response ) {
				$.getJSON(
					"<?= $this->cmsUri ?>/ajax/apps/createFilesForInstance/"+ response.id + "/", 
					{},
					function (data, textStatus) {
						if( textStatus === "success" && data.success === false ) {
							alert( data.message );
						}
					}
				);
			},
			onFillItem: function( item, data ) {
				item.attr("id", "block-" + data.id);
				item.attr("blockId", data.id);
			}
		}).start();
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

	$( '.edit' ).click( function( e ) {
		Object.create( Storm.Form ).extend( {
			object: Storm.buildPath( this ),
			form: $( '#MadonePageType-form' ),
			item:	$( this ).parents( '.a-unit-body' ),
			onFillItem: function( item, data ) {
				item.find( 'img.enabled' ).attr( 'src', data.enabled ?
				'/static/i/admin/icons/16/lamp-on.png' :
				'/static/i/admin/icons/16/lamp-off.png' )
			},
			onSubmit: function ( form, data, response ) {
				$.getJSON(
					"<?= $this->cmsUri ?>/ajax/apps/rename/"+ response.id + "/", 
					{},
					function (data, textStatus) {
						if( textStatus === "success" && data.success === false ) {
							alert( data.message );
						}
					}
				);
			}
		}).start();
	});
	
	$( '.delete' ).click( function( e ) {
		var title = $( this ).parents( '.a-unit:first' ).find( '.title' ).text();
		var id = $( this ).parents( '.a-unit-body:first' ).attr( 'StormObject' );
		var instance = this;
        if( confirm( 'Вы действительно хотите удалить приложение «' + title  + '»?' ) ) {
        	if( confirm( 'Удалить так же все сопутствующие файлы?' ) ) {
				$.getJSON(
					"<?= $this->cmsUri ?>/ajax/apps/deleteFilesByObjectId/"+ id + "/", 
					{},
					function (data, textStatus) {
						if(textStatus === "success" && data.success === true) {
							Storm.remove( Storm.buildPath( instance ), Function.delegate( instance, function() {
								$( this ).parents( '.a-unit:first' ).remove();
							}));
						}
						if( textStatus === "success" && data.success === false ) {
							alert( data.message );
						}
					}
				);
        	}
        	else {
        		Storm.remove( Storm.buildPath( instance ), Function.delegate( instance, function() {
					$( this ).parents( '.a-unit:first' ).remove();
				}));
        	}
		}
	});
	
	$(".duplicate").click(function(e){
		var instance = $(this).parents( '.a-unit:first' );
		var id = instance.find(".a-unit-body").attr("StormObject");
				
		$.getJSON(
			'<?=$this->cmsUri?>/ajax/apps/duplicate/'+ id +'/',
			{},
			function(data, textStatus){
				if( textStatus === "success" && data.success === true ) {
					var clone = $("#MadonePageType-item").clone(true).removeAttr('id');
					clone.find(".a-unit-body").attr("StormObject", data.data.id);
					clone.find(".title").text(data.data.title);
					clone.find(".app_classname").text(data.data.app_classname);
					clone.attr("blockId", data.data.id);
					clone.attr("id", "block-" + data.data.id);
					clone.find( 'img.enabled' ).attr( 'src', data.data.enabled ?
					'/static/i/admin/icons/16/lamp-on.png' :
					'/static/i/admin/icons/16/lamp-off.png' )
					clone.appendTo($(".installed.a-units")).show();
				}
				else {
					alert(data.message);
				}
			}
		);
	
	});
	
	$(".installed.a-units").sortable({
		helper: 'clone',
		placeholder: 'ui-sortable-placeholder',
		forcePlaceholderSize: true,
		stop: function(event, ui) {
			var objects = {};

            $.each( $(".installed.a-units").sortable( 'toArray' ), function( i, n ) {
                var id = n.split( '-' );
                try {
		              objects[ parseInt( id[1] ) ] = { position: i + 1 };
                } catch( e ) { };
            });

			$.post( "<?=$this->cmsUri?>/MadonePageType/update/", { objects: $.json.encode( objects ) }, function( r ) {
		        if( ! r.success )
		        {
		            alert( r.message );
		        }
	    	}, 'json' );
		}
	});
	
	
	/**
	 * Неустановленные модули
	 */
	$( '.install' ).click( function( e ) {
		var instance = $(this).parents( '.a-unit:first' );
		
		Object.create( Storm.Form ).extend({
			form: $( '#MadonePageType-install-form' ),
			object: 'MadonePageType',
			formPlace: $( '.found-apps.a-units' ),
			item:		$( '#MadonePageType-item' ),
			itemPlace:	$( '.installed.a-units' ),
			onFill: function(form, data) {
				form.find("input[name=title]").val( instance.find("div.title").text() );
				form.find("input[name=app_classname]").val( instance.find("p.app_classname").html() );

				form.find("input[name=priority]").val( instance.attr("priority") );
				
				if(instance.attr("has_text") == "1") {
					form.find("input[name=has_text]").attr('checked', 'checked');
				}
				else {
					form.find("input[name=has_text]").removeAttr('checked');
				}

				if(instance.attr("has_meta") == "1") {
					form.find("input[name=has_meta]").attr('checked', 'checked');
				}
				else {
					form.find("input[name=has_meta]").removeAttr('checked');
				}

				if(instance.attr("has_subpages") == "1") {
					form.find("input[name=has_subpages]").attr('checked', 'checked');
				}
				else {
					form.find("input[name=has_subpages]").removeAttr('checked');
				}

			},
			onHide: function( form ) {
				instance.show();
			},
			onShow: function() {
				instance.hide();
			},
			onSubmit: function(form, data, response) {
				instance.remove();
				
				if($(".a-units.found-apps .a-unit").length == 0) {
					$(".a-units.found-apps").remove();
				}
			},
			onFillItem: function( item, data ) {
				item.attr("id", "block-" + data.id);
				item.attr("blockId", data.id);
			}
		}).start();
	});
	
	$( '.remove' ).click( function( e ) {
		var title = $( this ).parents( '.a-unit:first' ).find( '.title' ).text();
		var instance = $( this ).parents( '.a-unit:first' );
        if( confirm( 'Вы действительно хотите удалить файлы приложжения «' + title  + '»?' ) ) {
			$.getJSON(
				"<?= $this->cmsUri ?>/ajax/apps/deleteFilesByClassName/"+ title + "/", 
				{},
				function (data, textStatus) {
					if(textStatus === "success" && data.success === true) {
						instance.remove();
						
						if($(".a-units.found-apps .a-unit").length == 0) {
							$(".a-units.found-apps").remove();
						}						
					}
					if( textStatus === "success" && data.success === false ) {
						alert( data.message );
					}
				}
			);
		}
	});
});
</script>
