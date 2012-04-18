<div id="admin-menu" class="sidebar_menu">
    <ul>
        <? $last_level = 1 ?>
        <? $z = 0?>
        <? foreach ($menu as $path => $item): ?>
            <? $level = count(explode('/', $path)) ?>
            <? if ($level > $last_level): ?><ul><? endif ?>
            <? if ($level <= $last_level) {
                for ($i = 0; $i < $last_level - $level; $i++) {
                    ?></ul></li><?
                }
            }
            ?>
            <? if($level <= $last_level && $z != 0):?>
            <?endif?>
            <li <?if($item->active()):?>class="active"<?endif?>>
                <a href="<?php echo  $item->getUri() ?>"><?php echo  $item->value ?></a>
            <? $last_level = $level ?>
            <?$z++?>
        <? endforeach ?>
        </li>
    </ul>
</div>