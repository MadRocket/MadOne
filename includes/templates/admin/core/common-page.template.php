<? @header( 'Content-Type: text/html;charset=utf-8' ) ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD
	HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title><? if( $this->has( 'title' ) ): ?><?=$this->title?> &mdash; <? endif; ?><?=Config::$i->site_title?> &mdash; MadOne</title>
        
        <link rel="stylesheet" type="text/css" href="/static/css/reset.css"/>
        <link rel="stylesheet" type="text/css" href="/static/css/admin/style.css"/>
        
        <!--[if IE 6]>
        <link rel="stylesheet" type="text/css" href="/static/admin/css/ie6.css"/>
        <![endif]-->
		
		<script type="text/javascript" src="/media/jquery-1.5.2.min.js"></script>
		<script type="text/javascript" src="/media/jquery.json.js"></script>
		<script type="text/javascript" src="/media/jquery.json-2.2.min.js"></script>		
		<script type="text/javascript" src="/media/improved.js"></script>
		<script type="text/javascript" src="/media/storm.js?20100707"></script>
		<script type="text/javascript" src="/media/mad.js"></script>
		<script type="text/javascript" src="/media/madone.js?20110503"></script>
		<script type="text/javascript">
			Madone.language = <?= json_encode( StormCore::getLanguage()->name ) ?>;
		</script>

		<script type="text/javascript" src="/media/jquery.tmpl-beta1/jquery.tmpl.min.js"></script>
		<script type="text/javascript" src="/media/jquery.tmpl-beta1/jquery.tmplplus.min.js"></script>		
		
		<script type="text/javascript" src="/media/swfobject.js"></script>

		<!-- Jquery UI -->
		<script type="text/javascript" src="/media/jquery-ui-1.8.4/jquery-ui-1.8.4.custom.min.js"></script>
		<link rel="stylesheet" type="text/css" href="/media/jquery-ui-1.8.4/ui-lightness/jquery-ui-1.8.4.custom.css"  />
				
		<script type="text/javascript" src="/media/interface/iutil.js"></script>
		<script type="text/javascript" src="/media/interface/idrag-fix.js"></script>
		<script type="text/javascript" src="/media/interface/idrop.js"></script>
		<script type="text/javascript" src="/media/interface/isortables.js"></script>
		<script type="text/javascript" src="/media/jquery.inestedsortable.js"></script>

		<script type="text/javascript" src="/media/jquery.form.js"></script>
		
		<!-- Загружалки -->
		<script type="text/javascript" src="/media/jquery.fileupload-3.4/jquery.fileupload.js"></script>
		<script type="text/javascript" src="/media/jquery.fileupload-3.4/jquery.fileupload-ui.js"></script>		
		<link rel="stylesheet" type="text/css" href="/media/jquery.fileupload-3.4/jquery.fileupload-ui.css"/>

		<script type="text/javascript" src="/media/uploadify-2.1.0/scripts/jquery.uploadify.v2.1.0.min.js"></script>
		<script type="text/javascript" src="/media/ajaxupload.3.5.js"></script>
		
		<!-- Визуальный редактор -->
		<script type="text/javascript" src="/media/ckeditor-3.6.1/ckeditor.js"></script>
		
		
		<script type="text/javascript" src="/media/initialization.js"></script>
		
		<!-- Лайтбокс. Используется в галереях для просмотра крупных изображений -->
		<script type="text/javascript" src="/media/jquery.fancybox-1.3.1/jquery.fancybox-1.3.1.pack.js"></script>
		<link rel="stylesheet" href="/media/jquery.fancybox-1.3.1/jquery.fancybox-1.3.1.css">
        
        <!--[if IE 6]>
        <script src="/media/DD_belatedPNG_0.0.8a-min.js"></script>
        <script> DD_belatedPNG.fix( '.png' ); </script>
        <![endif]-->
	</head>
    <body>
    	<div id="ajaxInfo">Жду ответ от сервера...</div>
        <div class="madone-header">
            <div class="left"><a href="<?= $this->cmsUri ?>/" title="Перейти к стартовой странице системы управления"><?=Config::$i->site_title?></a></div>
            <div class="right"><?= new Template( 'core/header-menu' ) ?></div>
        </div>
        <div class="madone-module">
        	<?= new Template( 'core/menu' ) ?><?=$this->content?>
        </div>
    </body>
    <script type="text/javascript">
        $(function(){
        	/* Информер активности ajax-гейта */
        	var timer = null;        	
        	$("#ajaxInfo").ajaxStart(function(){
        		timer = setTimeout('$("#ajaxInfo").show();', 500);
			});
        	$("#ajaxInfo").ajaxStop(function(){
        		clearTimeout(timer);
				$(this).hide();
			});
        });    
    </script>
</html>