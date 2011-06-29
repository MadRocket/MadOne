<? $this->startBuffering(); ?>

<?= new Template( 'showcase-navigation' ) ?>

<h1><?= $this->catalogItem->title ?></h1>

<? if( $this->catalogItem->images->count() ): ?>
	<div class="showcase-item-photos">
		<? foreach( $this->catalogItem->images->order( 'position' )->all() as $i ): ?>
			<a target="_blank" href="<?= $i->image->large->uri ?>"><?= $i->image->small->getHtml( array( 'alt' => $i->title ) ) ?></a>
		<? endforeach ?>
	</div>
<? endif ?>

<div class="showcase-item-price"><? if( $this->catalogItem->price ): ?><?= $this->catalogItem->price ?> руб<? else: ?>цена договорная<? endif ?></div>

<?= $this->catalogItem->description ?>

<? if( $this->catalogItem->movies->count() ): ?>
	<div class="showcase-item-videos">
		<!-- Документация к плееру: http://flv-player.net/players/normal/documentation/ -->
		<? foreach( $this->catalogItem->movies->order( 'position' )->all() as $i ): ?>
			<object class="movie" type="application/x-shockwave-flash" data="/static/swf/player_flv_maxi.swf" width="<?= $i->movie->width ?>" height="<?= $i->movie->height ?>">
				<param name="movie" value="/static/swf/player_flv_maxi.swf" />
				<param name="allowFullScreen" value="true" />
				<param name="FlashVars" value="flv=<?= $i->movie->uri ?>&amp;startimage=<?= $i->movie->preview_uri ?>&amp;width=<?= $i->movie->width ?>&amp;height=<?= $i->movie->height ?>" />
			</object>
		<? endforeach ?>
	</div>
<? endif ?>

<script src="/static/js/jquery-1.3.2.min.js"></script>
<script src="/static/js/highslide.js"></script>
<script>
	try {
		$( function() {
			$( 'a[href*=\/static\/uploaded\/images\/showcase]' ).click( function () {
				return hs.expand(this);
			} );
		} );
	} catch( e ) {};
</script>

<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ); ?>