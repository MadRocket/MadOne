<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?if( $this->page->meta_title ):?><?=$this->page->meta_title?><?else:?><?if( $this->page->lvl > 1 ):?><?=$this->page->title?> - <?endif?><?= Config::$i->{'site_title'} ?><?endif?></title>
<?if( $this->page->meta_keywords ):?><meta name="keywords" content="<?= $this->page->meta_keywords ?>"><?endif?>
<?if( $this->page->meta_description ):?><meta name="description" content="<?= $this->page->meta_description ?>"><?endif?>
<script src="/media/jquery-1.5.2.min.js"></script>
<script src="/static/js/improved.js"></script>