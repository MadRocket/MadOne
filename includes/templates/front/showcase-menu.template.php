	<?
	if( ! function_exists( 'renderPageChildren' ) ) {
	
		function renderPageChildren( $page, $selectedId ) {
			global $uri;
			global $hide;
			
			if( $page->getChildren() ) {
			?>
			<?foreach( $page->getChildren() as $c ):?>
				<div>
				<?if( $c->id == $selectedId && $hide ):?>
					<?= $c->title ?>
				<?else:?>
					<a href="<?= $uri ?><?= $c->uri ?>"><?= $c->title ?></a>
				<?endif?>
				<?= renderPageChildren( $c, $selectedId )?>
				</div>
			<?endforeach?>
			<?
			}
		}
	}
	?>
	
	<?
	global $uri;
	$uri = MadonePages( array( 'type__app_classname' => 'MadoneShowcaseApplication' ) )->first()->uri;

	global $hide;
	$hide = $this->has( 'catalogItem' ) ? false : true;
	
	$query = MadoneShowcaseSections()->filterLevel( 2 );
	if( $this->has( 'catalogSection' ) ) {
		$query = $query->embraceBranch( $this->catalogSection );
	}
	
	

	$pages = $query->filter( array( 'enabled' => true ) )->kiOrder()->tree();
	?>
	<? foreach( $pages as $p ): ?>
		<div>
		<? if( $this->has( 'catalogSection' ) && $this->catalogSection->id == $p->id ): ?>
			<?= $p->title ?>
		<? else: ?>
			<a href="<?= $uri ?><?= $p->uri ?>/"><?= $p->title ?></a>
		<? endif ?>
		<?= renderPageChildren( $p, $this->has( 'catalogSection' ) ? $this->catalogSection->id : 0 ) ?>
		</div>
	<? endforeach ?>