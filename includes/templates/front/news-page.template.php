<? $this->startBuffering(); ?>

<? foreach( $this->paginator->objects as $i ): ?>
	<h2><a style="font-size: 16px;" href="/news/news<?= $i->id ?>/"><?= $i->title ?></a> <small style="color: #888"><?= $i->date ?></small></h2>
	<div style="margin-bottom: 20px;">
	<?= $i->text ?>
	</div>
<? endforeach ?>

<?= $this->paginator ?>

<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ); ?>