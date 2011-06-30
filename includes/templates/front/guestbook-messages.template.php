<? if( $this->paginator ): ?>
	<?foreach( $this->paginator->getObjects() as $i ):?>
		<p><?= $i->date ?> — <?= $i->name ?></p>
		<p><?= $i->text ?></p>
		<div>
		<? foreach( $i->photos->order( 'position' )->all() as $photo ): ?>
			<a target="_blank" href="<?= $photo->image->large->uri ?>"><?= $photo->image->small->getHtml() ?></a>
		<? endforeach ?>
		</div>
		<br><hr/><br>
	<?endforeach?>
	<?= $this->paginator ?>
<? endif ?>