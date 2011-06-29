<? $this->startBuffering(); ?>

<?= new Template( 'showcase-navigation' ) ?>

<h1><?= $this->catalogSection->title ?></h1>

<? if( $this->paginator->count ): ?>
	<?= new Template( 'showcase-item-list', array( 'uri' => $this->page->uri ) ) ?>
	<?= $this->paginator ?>
<? else: ?>
	<p>К сожалению, здесь ничего нет.</p>
<? endif ?>

<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ); ?>