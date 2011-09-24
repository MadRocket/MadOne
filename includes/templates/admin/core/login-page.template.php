<? @header( 'Content-Type: text/html;charset=utf-8' ) ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <title><?=Config::$i->site_title?> &mdash; MadOne</title>
        <link rel="stylesheet" type="text/css" href="/static/css/reset.css"/>
        <link rel="stylesheet" type="text/css" href="/static/css/admin/style.css"/>
        <!--[if IE 6]>
        <link rel="stylesheet" type="text/css" href="/static/css/admin/ie6.css"/>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="/static/css/admin/login.css"/>
    
        <script type="text/javascript" src="/media/jquery-1.5.2.min.js"></script>
        <!--[if IE 6]>
        <script language="JavaScript" type="text/javascript" src="/media/DD_belatedPNG_0.0.8a-min.js"></script>
        <script type="text/javascript"> DD_belatedPNG.fix('.png') </script>
        <![endif]-->
    </head>
    <body>
        <div class="madone-header">
            <img class="png" src="/static/i/admin/login-logo.png" width="147" height="42" alt="Система управления сайтом MadOne" />
        </div>
        <div class="madone-content">
            <form method="post" class="madone-login-form">
                <div class="block"><label>Имя</label><input tabindex="1" type="text" name="_madone_login" value="<?= Mad::vars( '_madone_login' ) ?>"/></div>
                <div class="block"><label>Пароль</label><input tabindex="2" type="password" name="_madone_password"/></div>
                <div class="block">
                	<button tabindex="3" type="submit" class="styled-button"><b><b>Войти</b></b></button>
                </div>
                <? if( MadoneSession::getInstance()->getLoginAttempt() ):?><div class="block"><p class="error-msg">Неправильное имя или пароль.</p></i></div><?endif?>
            </form>
        </div>
    </body>
    <script type="text/javascript">
    $( document ).ready( function() {
        $( 'input[name=_madone_login]' ).focus();
    });
    </script>
</html>