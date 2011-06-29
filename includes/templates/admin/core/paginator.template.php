<? if( $this->paginator->pages ):?>
    <div class="paginator">
    <? foreach( $this->paginator->pages as $i ):?><? if( array_key_exists('left', $i) && $i['left'] ):?><a class="larr" href="<?=$i['uri']?>">&larr;</a><? elseif( array_key_exists('right', $i) && $i['right'] ):?><a class="rarr" href="<?=$i['uri']?>">&rarr;</a><? elseif( array_key_exists('ellipsis', $i) && $i['ellipsis'] ):?><a class="ellipsis">...</a><? elseif( array_key_exists('uri', $i) && $i['uri'] ):?><a href="<?=$i['uri']?>"><?=$i['title']?></a><?else:?><a class="active"><?=$i['title']?></a><?endif?><?endforeach?>
    </div>
<? endif?>