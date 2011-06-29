<div class="module-content">
<p style="margin-left: 9px;">
	<label for="setting_module">После входа в систему</label>
	<select id="setting_module">
	<option value="0">показать страницу приветствия</option>
	<?foreach( MadoneModules( array( enabled => true ) )->order( 'position' )->all() as $m ):?>
	    <option value="<?=$m->id?>" <?= MadoneSession::getInstance()->getUser()->setting_module && MadoneSession::getInstance()->getUser()->setting_module->id == $m->id ? 'selected' : '' ?> >открыть раздел <?=$m->title?></option>
	<?endforeach?>
	</select>
</p>
</div>

<script type="text/javascript">

$(document).ready( function() {
    $("#setting_module").bind( 'change', function( e ) {
        
        e.preventDefault();
        
        $.post( "<?=$this->ajaxUri?>/module/", { module: $( this ).val() }, function( r ) {
            if( ! r.success ) {
                alert( r.message );
            }
        }, 'json' );
    } );
} );

</script>
