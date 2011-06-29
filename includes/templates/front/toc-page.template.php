<?=$page->text?>

<?foreach( $this->paginator->objects as $page ):?>
	<h3><a href="<?=$page->uri?>"><?= $page->title ?></a></h3>
	<?if( preg_match( '/<p>(.*?)<\/p>/i', $page->text, $m ) ):?>
	<p><?= strip_tags( str_replace( '<br />', "\n", $m[1] ) ) ?> <a href="<?=$page->uri?>">Читать &rarr;</a></p>
	<?endif?> 
<?endforeach?>

<?= $this->paginator ?>