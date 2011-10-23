<?
require_once(__DIR__."/../outer/router/klein.php");
/**
 * Прародитель приложений
 */
class AbstractApplication {
    /**
        Запуск приложения!
            $page - соответствующий объект структуры сайта
            $uri - путь к искомой странице _внутри_ приложения.
        Возвращает true, если страница обработана этим приложением, false, если страница приложением не обработана.
    */
    function run( MadonePage $page, $uri = '' ) {
        @header( 'Content-Type: text/html;charset=utf-8' );
        ?>
        <h1><?=$page->title?></h1>
        <p><i><?=$uri?></i></p>
        <p><?=get_class( $this )?></p>
        <ul>
        <? foreach( MadonePages( array( 'enabled' => true ) )->kiOrder()->all() as $p ):?>
            <li style="margin-left:<?=( $p->lvl - 1 ) * 30 ?>px">
            <?if( $page->id == $p->id ):?>
                <?=$p->title?>
            <?else:?>
                <a href="<?=$p->uri?>"><?=$p->title?></a>
            <?endif?>
            </li>
        <? endforeach?>
        </ul>
        <?

        return true;
    }
}