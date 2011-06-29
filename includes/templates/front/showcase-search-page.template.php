<? $this->startBuffering(); ?>

<? if( ! $this->query || ( $this->paginator && ! $this->paginator->objects ) ): ?>
	<?= $this->page->text ?>
<? else: ?>
		<h1><? if( $this->paginator && $this->paginator->count ): ?>По запросу <i>«<?= $this->query ?>»</i> <?= Mad::decline( $this->paginator->count, '', 'нашлась одна позиция', 'нашлось %n позиции', 'нашлось %n позиций' ) ?></span><? else: ?><?= $this->page->title ?><? endif ?></h1>
<? endif ?>

<form class="showcase-search-form" method="get" action="<?= $this->page->uri ?>/">
	<input type="search" name="q" value="<?= $this->queryHE ?>"> <button type="submit">Найти</button>
</form>
		
<? if( $this->query ): ?>
	<? if( $this->paginator && $this->paginator->objects ): ?>
	
		<?= new Template( 'showcase-item-list', array( 'uri' => MadonePages( array( 'type__app_classname' => 'MadoneShowcaseApplication' ) )->first()->uri ) ) ?>

		<?= $this->paginator ?>
	<? else: ?>

		<p>По запросу <b><i>«<?= $this->query ?>»</i></b> ничего не найдено.</p>
		<p>Пожалуйста, переформулируйте запрос.</p>

	<? endif ?>
<? endif ?>

<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ); ?>