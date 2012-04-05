<div id="install_menu">
    <ul>
    <? $show_next = TRUE; ?>
    <? foreach($menu as $key=>$item): ?>
        <li id="user_menu_<?=str_replace('/','_',trim($key,'/'))?>"<?if($item->active()):?> class="active"<?endif?>><? if($show_next):?><a href="<?=$item->getUri()?>"><?=$item->value?></a><?else:?><?=$item->value?><?endif;?></li>
        <?  // Special flag. You can roll back, but never forward.
            $item->active() && $show_next = FALSE; ?>
    <? endforeach; ?>
    </ul>
</div>