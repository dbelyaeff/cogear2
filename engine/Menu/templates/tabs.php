<div class="menu" id="<?php echo  $menu->getName() ?>">
    <ul class="tabs">
        <? foreach ($menu as $item): ?>
            <li<?if($item->class){?> class="<?php echo $item->class?>"<?}?>>
                <a href="<?php echo  $item->getUri() ?>"><?php echo  $item->value ?></a>
            </li>
        <? endforeach ?>
    </ul>
</div>