<div id="user_menu">
    <ul>
    <? foreach($menu as $key=>$item): ?>
        <li id="user_menu_<?=str_replace('/','_',trim($key,'/'))?>" <?if(is_double($item->order)){?> class="subitem"<?}?>><a <?if($item->active()):?> class="active"<?endif?> href="<?=$item->getUri()?>"><?=$item->value?></a></li>
    <? endforeach; ?>
    </ul>
</div>