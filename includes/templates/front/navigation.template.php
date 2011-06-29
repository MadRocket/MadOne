<?
if(! function_exists('renderMenuItem')) {
	function renderMenuItem( $template, $page )
	{
		?>
		<div>
		<?if( $page->uri == Mad::getUriPath() ):?>
			<?=$page->title?>
		<?else:?>
			<a href="<?=$page->uri?>/"><?=$page->title?></a>
		<?endif?>
		
		<?if( $page->type->app_classname == 'MadoneShowcaseApplication' ):?>
		<?= new Template( 'showcase-menu' ) ?>
		<?endif?>
		
		<? foreach( $page->getChildren() as $subpage ) {
			renderMenuItem( $template, $subpage );
		} ?>
		</div>
		<?
	}
}
?>

<? foreach( MadonePages( array( 'enabled' => true, 'menu' => true ) )->filterLevel( 2, 0 )->follow(2)->kiOrder()->tree() as $p ):?>
	<? renderMenuItem( $this, $p ) ?>
<? endforeach?>