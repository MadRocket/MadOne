<? if( $this->catalogSection->lvl > 2 ): ?>
<?
$nav = array();
$sections = MadoneShowcaseSections( array( 'enabled' => true ) )->filterParents( $this->catalogSection );
if( $this->has( 'catalogItem' ) ) {
	$sections = $sections->embrace( $this->catalogSection );
}
?>
<? foreach( $sections->filterLevel( 2, 0 )->kiOrder()->all() as $i ): ?>
	<? ob_start() ?>
	<a href="<?= $this->page->uri?><?= $i->uri ?>/"><?= $i->title ?></a>
	<? $nav[] = ob_get_clean(); ?>
<? endforeach ?>
<div class="navpath">
	<?= join("<span>&rarr;</span>", $nav) ?>
</div>					
<? endif ?>
