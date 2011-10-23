<div class="module-buttons">
	
</div>
<div class="module-content">
<? if( $this->items ): ?>
    <div id="textblocks" class="a-units" stormModel="MadoneTextBlock">
    </div>
    
	<div class="a-unit" id="textBlockItem" style="display: none;">
		<div class="a-unit-body">
			<div class="actions">
				<img title="Редактировать" class="edit" width="16" height="16" src="/static/i/admin/icons/16/pencil.png"/>
				<img title="Включить/Выключить" class="enabled" stormField="enabled" width="16" height="16" src="/static/i/admin/icons/16/lamp-on.png"/>
			</div>
			<h2 stormGrid="title"></h2>
			<p stormGrid="description"></p>
		</div>
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
		var grid = Object.create( Storm.Grid ).extend({
			model: 'MadoneTextBlock',
			place: $('#textblocks'),
			item: $("#textBlockItem"),
			items: <?= json_encode(Mad::getJsonSafe($this->items)) ?>,
			itemDataMapper: {
				'title': 'name',
				'description': function(data){
					return data.preview ? data.preview : "&nbsp;";
				}
			},
			editHandler: function( e ) {
				Object.create( Storm.Form ).extend( {
					object: Storm.buildPath( this ),
					form: $( '#block-form' ),
					item:	$( this ).parents( '.a-unit-body' )
				}).start();
			}
		});
		
		grid.start();
	} )
    </script>
<? else: ?>
	<p>Ни одного текстового блока не найдено.</p>
<? endif ?>
</div>
