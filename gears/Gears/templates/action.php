<?php; ?>
<div class="page-header"><h2><?php echo t('Please, wait…') ?></h2></div>
<?php foreach ($gears as $gear): ?>
    <?php
    $type = 'danger';
    switch ($do) {
        default:
        case 'enable':
            if ($gear->status() == Gears::EXISTS) {
                $result = $gear->install();
                if ($result->success) {
                    $type = 'success';
                    $result2 = $gear->enable();
                    if (!$result2->success) {
                        $result->success = FALSE;
                    } else {
                        $type = 'info';
                    }
                    $result->message .= '<p>' . $result2->message;
                }
            } else {
                $result = $gear->enable();
                if ($result->success) {
                    $type = 'info';
                }
            }
            break;
        case 'disable':
            $result = $gear->disable();
            if ($result->success) {
                $type = 'success';
            }
            break;
        case 'install':
            $result = $gear->install();
            if ($result->success) {
                $type = 'success';
            }
            break;
        case 'uninstall':
            $result = $gear->uninstall();
            if ($result->success) {
                $type = 'success';
            }
            break;
    }
    ?>
    <div class="alert alert-<?php echo $type ?>">
        <b><?php echo t($gear->name) ?></b>
        <p><?php echo $result->message; ?></p>
        <?php if ($gear->status() == Gears::DISABLED): ?>
            <a href="<?php echo l('/admin/gears/') . '?do=enable&gears=' . $gear->gear; ?>" class="btn btn-primary"><?php echo t('Enable') ?></a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<p><a href="<?php echo l('/admin/gears/') ?>" class="btn">&larr; <?php echo t('Back') ?></a></p>