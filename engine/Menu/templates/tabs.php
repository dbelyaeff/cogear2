<div class="menu" id="<?= $menu->getName() ?>">
    <ul class="tabs">
        <? foreach ($menu as $item): ?>
            <li<?if($item->class){?> class="<?=$item->class?>"<?}?>>
                <a href="<?= $item->getUri() ?>"><?= $item->value ?></a>
            </li>
        <? endforeach ?>
    </ul>
</div>