<div class="page-header"><h2><?php echo t('Пожалуйста, подождите…') ?></h2></div>
<?php foreach ($gears as $gear): ?>
    <?php
    $type = 'danger';
    switch ($do) {
        default:
        case 'enable':
            $result = $gear->enable();
            if ($result->success) {
                $type = 'success';
            }
            break;
        case 'disable':
            $result = $gear->disable();
            if ($result->success) {
                $type = 'success';
            }
            break;
    }
    ?>
    <div class="alert alert-<?php echo $type ?>">
        <b><?php echo $gear->name ?></b>
        <p><?php echo $result->message; ?></p>
    </div>
<?php endforeach; ?>
<p><a href="<?php echo server('referer') ?>" class="btn">&larr; <?php echo t('Назад') ?></a></p>