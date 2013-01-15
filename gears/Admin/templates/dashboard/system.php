<?php
$gears = new Gears(GEARS, array(
            'remove' => FALSE,
            'charge' => TRUE
        ));
?>
<div id="system">
<b><?php echo t('Версия системы: ') ?></b> <?php echo COGEAR ?><br/>
<b><?php echo t('Последнее обновление: ') ?></b> <?php echo date('H:i d.m.Y', filemtime(ROOT . DS . 'index' . EXT)) ?><br/>
<div class="well">
<span class="left"><b><?php echo t('Всего шестерёнок: '); ?></b></span><span class="right"><?php echo HTML::a(l('/admin/gears/'), $gears->count(),array('class'=>'label')) ?></span>
<span class="left"><b><?php echo t('Активных: '); ?></b></span><span class="right"><?php echo HTML::a(l('/admin/gears/enabled'), $gears->filter(Gears::ENABLED)->count(),array('class'=>'label label-success')) ?></span>
<span class="left"><b><?php echo t('Неактивных: '); ?></b></span><span class="right"><?php echo HTML::a(l('/admin/gears/disabled'), $gears->filter(Gears::DISABLED)->count(),array('class'=>'label label-important')) ?></span>
<span class="left"><b><?php echo t('Ядра: '); ?></b></span><span class="right"><?php echo HTML::a(l('/admin/gears/core'), $gears->filter(Gears::CORE)->count(),array('class'=>'label label-inverse')) ?></span>
    <div class="clearfix"></div>
</div>
</div>
<style>
    .left, .right{
        width: 50%;
        display: block;
        float: left;
    }
    .left {
        text-align: right;
    }
    .right{
        text-align: left;
    }
    .right a {
        margin-left: 5px;
        width: 1.4em;
        text-align: center;
    }
</style>