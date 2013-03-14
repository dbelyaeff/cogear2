<h1><?php echo icon('refresh') . ' ' . t('Обновление системы') ?></h1>
<div class="well">
    <?php echo t('Последний раз проверено: <i>%s</i>', df(config('admin.update.lastcheck', filemtime(ROOT . DS . 'index.php')))) ?>
    <p><a href="<?php echo l(TRUE) . e(array('action' => 'check')) ?>" class="btn"><?php echo t('Проверить наличие обновлений') ?></a>
</div>
<p><?php echo t('Версия установленной системы: <b>%s</b>', COGEAR) ?></p>
<p><?php echo t('Последняя системы: <b>%s</b>', config('admin.update.repo.major', COGEAR)) ?>
    <?php if (version_compare(config('admin.update.repo.major'), COGEAR) == 1):  ?>
    <a href="?action=update_core" class="btn btn-primary"><?php echo t('Обновить')?></a>
    <?php else:?>
    <i class="well well-small"><?php echo t('Обновление не требуется')?></i>
    <?php endif; ?>
</p>