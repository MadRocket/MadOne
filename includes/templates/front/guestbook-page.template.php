<? $this->startBuffering(); ?>

<? if( $this->mode == 'added' ): ?>
	<h1><?= $this->page->title ?></h1>
	<p>Ваше сообщение отправлено, спасибо!</p>
<? else: ?>
	<?= $this->page->text ?>
	<? if( $this->mode == 'notext' ): ?>
		<p style="color:red;">Пожалуйста, введите текст сообщения.</p>
	<? elseif( $this->mode == 'nocaptcha' ): ?>
		<p style="color:red;">Неправильно введен защитный код. И укажите фотографии заново.</p>
	<? else: ?>
	<?= new Template( 'guestbook-messages' ) ?>
	<? endif ?>
	<?= new Template( 'guestbook-form' ) ?>
<? endif ?>
<script src="/static/js/jquery-1.3.2.js"></script>
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