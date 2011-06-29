<? $this->startBuffering() ?>

<?= $this->page->text ?>

<!-- Список разделов галереи -->				
<div class="gallery-sections">
<small>разделы галереи</small>
<? foreach( MadoneGallerySections()->filterLevel( 2, 0 )->kiOrder()->all() as $i ): ?>
	<?if( $this->section && $this->section->id == $i->id ):?>
		<span><?= $i->title ?></span>
	<?else:?>
		<a href="<?=$this->page->uri?><?=$i->uri?>/"><?= $i->title ?></a>
	<?endif?>
<? endforeach ?>
</div>

<? if( $this->section ): ?>
	<? if( $this->image ): ?>
		<!-- Выбраная картинка -->
		<div class="gallery-image" style="text-align: center; margin-bottom: 30px;">
		<?= $this->image->image->large->getHTML() ?>
		</div>
	<? endif ?>

	<!-- Превью картинок выбранного раздела галереи -->
	<div class="gallery">
	<? foreach( $this->paginator->objects as $i ): ?>
		<ins class="thumbnail">
	        <div class="r">
	            <a href="<?= $i->image->cms->uri ?>"><?= $i->image->cms->getHTML(array('alt' => $i->title, 'class' => "photo")) ?></a><br />
	            <?= $i->title ?>
	        </div>
	    </ins>
	<? endforeach ?>
	</div>
	
	<?= $this->paginator ?>
<? else: ?>
	<!-- раздел не выбран показываем список разделов -->
	<h2>Фотогалереи</h2>
	<div class="gallery">
	<? foreach( MadoneGallerySections(array('enabled' => true))->filterLevel( 2, 0 )->kiOrder()->all() as $i ): ?>
		<? $first_photo_form_gallery = $i->images->first(); ?>
		<? if($first_photo_form_gallery != null): ?>
		<ins class="thumbnail">
	        <div class="r">
	            <a href="<?=$this->page->uri?><?=$i->uri?>/"><?= $first_photo_form_gallery->image->cms->getHTML(array('alt' => $first_photo_form_gallery->title)) ?></a><br />
				<a href="<?=$this->page->uri?><?=$i->uri?>/"><?= $i->title ?></a>
	        </div>
	    </ins>
	    <? endif ?>
	<? endforeach ?>
	</div>
<? endif ?>
<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ) ?>