<script type="text/javascript">
$(function() {
    $('.madone-menu-title span').click( function( e ) {
    	$( '.madone-menu-title').addClass('madone-menu-title-active');
        $( '.madone-menu-items' ).show();
	});
    
    $('.madone-menu-title').bind( 'mouseleave', function( e ) {
        $( '.madone-menu-items' ).hide();
    	$( '.madone-menu-title').removeClass('madone-menu-title-active');        
    });
    
    if($("#quick-help").length != 0 ) {
	    $(".helpbutton").show().click(function(e){
	    	$("#quick-help").toggle();
	    });    
    }
});
</script>
<button class="helpbutton" title="Помощь по текущему модулю"><b>&nbsp;</b></button>
<h1 class="madone-menu-title" title="Меню. Кликните, чтобы открыть."><div class="madone-menu-items">
    <? foreach( $this->menuItems as $i ): ?>
        <? if( $i['selected'] ): ?>
        <a class="active"><i><?=$i['title']?></i></a>
        <? else: ?>
        <a href="<?=$i['uri']?>"><i><?=$i['title']?></i></a>
        <? endif; ?>
    <? endforeach ?>
</div><span><?= $this->title ?></span></h1>
