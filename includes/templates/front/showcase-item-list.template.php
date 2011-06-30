<table class="showcase-layout">
<?foreach( $this->paginator->getObjects() as $i ):?>
	<? $image = $i->images->first(); ?>
	<tr>
		<? if( $image ): ?>
		<td class="image">
			<a href="<?= $this->uri ?><?= $i->section->uri ?>/<?= $i->id ?>/"><?= $image->image->small->getHtml() ?></a>
		</td>
		<? else: ?>
		<td class="no-image">
			нет фото
		</td>
		<? endif ?>
		<td>
			<h2><a href="<?= $this->uri ?><?= $i->section->uri ?>/<?= $i->id ?>/"><?= $i->title ?></a></h2>
			<p><?= $i->short_description ?></p>
		</td>
		<td class="price"><? if( $i->price ): ?><?= $i->price ?> руб<? else: ?>цена договорная<? endif ?></td>
	</tr>
<?endforeach?>
</table>