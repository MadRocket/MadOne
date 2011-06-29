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

.found-modules {
    margin-bottom: 15px;
}

h3 {
	margin-bottom: 10px;
}
.found-modules .actions {
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
	<button class="MadoneModule-create styled-button"><b><b>Создать модуль</b></b></button>
</div>

<div class="module-content">

<div class="createFormPlace"></div>

<? if(count($this->newmodules) > 0): ?>
<h3>В системе найдены неустановленные модули<br><small>Модули обычно тесно связаны с приложениями, редактировать их лучше совместно</small></h3>
<div class="a-units found-modules" stormModel="MadoneModule">
	<? foreach( $this->newmodules as $classname ): ?>
		<div class="a-unit">
		<div class="a-unit-body">
			<div class="actions">
				<img title="Установить" class="install" width="16" height="16" src="/static/i/admin/icons/16/plus.png"/>
				<img title="Удалить" class="remove" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
			</div>
			<div class="title"><?= $classname ?></div>
		</div>
		</div>
	<? endforeach ?>
</div>
<? endif ?>

<h3>Установленные модули</h3>
<div class="installed a-units" stormModel="MadoneModule">
	<? foreach( $this->modules as $m ): ?>
		<div class="a-unit" id="block-<?=$m->id?>" blockId="<?=$m->id?>">
		<div class="a-unit-body<?= $m->enabled ? '' : ' disabled'?>" stormObject="<?=$m->id?>">
			<div class="actions">
				<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
				<img title="Продублировать" class="duplicate" width="16" height="16" src="/static/i/admin/icons/16/copy.png"/>
				<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-<?= $m->enabled ? 'on' : 'off'?>.png"/>
				<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
			</div>
			<div class="title"><?=$m->title?></div>
			<p stormHtml="name"><?= $m->name ?></p>
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

<div id="MadoneModule-item" class="a-unit" style="display: none;">
	<div class="a-unit-body" stormObject="">
		<div class="actions">
			<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
			<img title="Продублировать" class="duplicate" width="16" height="16" src="/static/i/admin/icons/16/copy.png"/>
			<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			<img title="Удалить" class="delete" width="16" height="16" src="/static/i/admin/icons/16/cross.png"/>
		</div>
		<div stormHtml="title" class="title"></div>
		<p stormHtml="name" class="name"></p>
	</div>
</div>

<div id="MadoneModule-form" class="a-unit-form" style="display: none;">
	<div class="block"><label>Название модуля:</label><input name="title" type="text" class="width-100" /></div>
	<div class="block"><label>Название по-английски:</label><input name="name" type="text" class="width-100" /></div>
	<div class="block"><label>Имя класса:</label><input name="classname" type="text" class="width-100" /></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Сохранить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>
	</div>
</div>

<div id="MadoneModule-install-form" class="a-unit-form" style="display: none;">	
	<div class="block"><label>Название модуля:</label><input name="title" type="text" class="width-100" /></div>
	<div class="block"><label>Название по-английски:</label><input name="name" type="text" class="width-100" /></div>
	<div class="block"><label>Имя класса:</label><span name="classname"></span><input name="classname" type="hidden" /></div>
	<div class="block">
		<button class="submit small-styled-button"><b><b>Установить</b></b></button>
		<button class="cancel small-styled-button"><b><b>Отмена</b></b></button>		
	</div>
</div>


<script type="text/javascript">
$( function() {
	/**
	 * Установленные модули
	 */

	$( '.MadoneModule-create' ).click( function( e ) {
		Object.create( Storm.Form ).extend( {
			form: $( '#MadoneModule-form' ),
			object: 'MadoneModule',
			formPlace: $( '.createFormPlace' ),
			item:		$( '#MadoneModule-item' ),
			itemPlace:	$( '.installed.a-units' ),
			onSubmit: function( form, data, response ) {
				$.getJSON(
					"<?= $this->cmsUri ?>/ajax/modules/createFilesForInstance/"+ response.id + "/", 
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
		} ).start();
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
			form: $( '#MadoneModule-form' ),
			item:	$( this ).parents( '.a-unit-body' ),
			onFillItem: function( item, data ) {
				item.find( 'img.enabled' ).attr( 'src', data.enabled ?
				'/static/i/admin/icons/16/lamp-on.png' :
				'/static/i/admin/icons/16/lamp-off.png' )
			},
			onSubmit: function ( form, data, response ) {
				$.getJSON(
					"<?= $this->cmsUri ?>/ajax/modules/rename/"+ response.id + "/", 
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
        if( confirm( 'Вы действительно хотите удалить модуль «' + title  + '»?' ) ) {
        	if( confirm( 'Удалить так же все сопутствующие файлы?' ) ) {
				$.getJSON(
					"<?= $this->cmsUri ?>/ajax/modules/deleteFilesByObjectId/"+ id + "/", 
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
			'<?=$this->cmsUri?>/ajax/modules/duplicate/'+ id +'/',
			{},
			function(data, textStatus){
				if( textStatus === "success" && data.success === true ) {
					var clone = $("#MadoneModule-item").clone(true).removeAttr('id');
					clone.find(".a-unit-body").attr("StormObject", data.data.id);
					clone.find(".title").text(data.data.title);
					clone.find(".name").text(data.data.name);
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

			$.post( "<?=$this->cmsUri?>/MadoneModule/update/", { objects: $.json.encode( objects ) }, function( r ) {
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
			form: $( '#MadoneModule-install-form' ),
			object: 'MadoneModule',
			formPlace: $( '.found-modules.a-units' ),
			item:		$( '#MadoneModule-item' ),
			itemPlace:	$( '.installed.a-units' ),
			onFill: function(form, data) {
				form.find("span[name=classname]").html( instance.find(".title").html() );
				form.find("input[name=classname]").val( instance.find(".title").html() );
			},
			onHide: function( form ) {
				instance.show();
			},
			onShow: function() {
				instance.hide();
			},
			onSubmit: function(form, data, response) {
				instance.remove();
				
				if($(".a-units.found-modules .a-unit").length == 0) {
					$(".a-units.found-modules").remove();
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
        if( confirm( 'Вы действительно хотите удалить файлы модуля «' + title  + '»?' ) ) {
			$.getJSON(
				"<?= $this->cmsUri ?>/ajax/modules/deleteFilesByClassName/"+ title + "/", 
				{},
				function (data, textStatus) {
					if(textStatus === "success" && data.success === true) {
						instance.remove();
						
						if($(".a-units.found-modules .a-unit").length == 0) {
							$(".a-units.found-modules").remove();
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
