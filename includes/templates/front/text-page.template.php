<?@header( 'Content-Type: text/html;charset=utf-8' )?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<?= new Template( 'meta' ) ?>
	<link rel="stylesheet" href="/static/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="/static/css/generic.css" type="text/css" />
	<link rel="stylesheet" href="/static/css/style.css" type="text/css" />
	<link rel="stylesheet" href="/static/css/jquery.lightbox-0.5.css" type="text/css" />
	<!--[if IE 6]>
		<link rel="stylesheet" href="/static/css/ie6.css" type="text/css" />
	<![endif]-->
</head>
<body>
<div id="container">
	<div id="logo-container">
		<h1>Ваш сайт</h1>
	</div>
	<div id="top-navigation">
		<?= new Template('navigation'); ?>
	</div>

	<div id="side-navigation">
	</div>

	<?= $this->page->text ?>
	
	<?= new Template('footer'); ?>	
</div>
</body>
<script type="text/javascript" src="/static/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/static/js/jquery.lightbox-0.5.min.js"></script>
<script type="text/javascript">
$( function() {
	$( 'a[rel=lightbox]' ).lightBox();
});
</script>
</html>