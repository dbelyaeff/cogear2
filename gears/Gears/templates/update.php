<?php $result =  $gear->versionUpdate();?>
<div class="page-header"><h2><?php echo t('Пожалуйста, подождите…') ?></h2></div>
    <div class="alert alert-<?php echo $result ? 'success' : 'danger' ?>">
        <b><?php echo $gear->name ?></b>
        <p><?php echo $result ? t('Обновление прошло успешно!') : t('Не удалось обновить шестерёнку.')?></p>
    </div>
<p><a href="<?php echo server('referer') ?>" class="btn">&larr; <?php echo t('Назад') ?></a></p>