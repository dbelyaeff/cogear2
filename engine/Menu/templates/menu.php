<div class="menu" id="<?= $menu->getName() ?>">
    <ul class="tabs">
        <? $last_level = 1 ?>
        <? $z = 0?>
        <? foreach ($menu as $path => $item): ?>
            <? $level = count(explode('.', $path)) ?>
            <? if ($level > $last_level): ?><ul class="tabs"><? endif ?>
            <? if ($level <= $last_level) {
                for ($i = 0; $i < $last_level - $level; $i++) {
                    ?></ul></li><?
                }
            }
            ?>
            <? if($level <= $last_level && $z != 0):?>
            <?endif?>
            <li<?if($item->class){?> class="<?=$item->class?>"<?}?>>
                <a href="<?= $item->link ?>"><?= $item->text ?></a>
            <? $last_level = $level ?>
            <?$z++?>
        <? endforeach ?>
        </li>
    </ul>
</div>