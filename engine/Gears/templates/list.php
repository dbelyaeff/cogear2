<? foreach ($packages as $package => $gears): ?>
    <div class="package collapsible" id="package-<?php echo  $package?>">
        <h1><?php echo  t($package,'Packages')?> <a href="#" class="edit handler">-</a></h1>
        <div class="gears">
            <? $tpl = new Template('Gears.item') ?>
            <? foreach ($gears as $name => $gear): ?>
                <?
                $tpl->reset();
                $tpl->assign($gear->info());
                echo $tpl->render();
                ?>
            <? endforeach; ?>
        </div>
    </div>
<? endforeach; ?>
