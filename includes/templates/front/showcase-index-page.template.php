<? $this->startBuffering(); ?>

<?= $this->page->text ?>
<? foreach( MadoneShowcaseSections( array( 'enabled' => true ) )->filterLevel( 2 )->kiOrder()->all() as $i ): ?>
	<h3><a href="<?= $this->page->uri ?><?= $i->uri ?>/"><?= $i->title ?></a></h3>
<? endforeach ?>

<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ); ?>
