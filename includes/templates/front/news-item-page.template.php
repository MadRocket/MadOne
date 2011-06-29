<? $this->startBuffering(); ?>

<?= $this->item->text ?>

<? $this->page->text  = $this->stopBuffering(); ?>
<? $this->page->title  = $this->item->title; ?>
<? $this->replaceWith( 'text-page' ); ?>