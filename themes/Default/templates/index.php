<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php echo theme('head') ?>
    </head>
    <body>
        <?php echo theme('before') ?>
        <div class="container">
            <div class="row">
                <div class="span12" id="header">
                    <a href="<?php echo l(); ?>"><img src="<?php echo l($theme->folder) ?>/img/logo.png"/></a>
                    <?php echo theme('header') ?>
                </div>
            </div>
            <div class="row">
                <?php $sidebar = theme('sidebar') ?>
                <div class="span<?php echo $sidebar ? '8' : '12';?>" id="content">
                <?php echo theme('info') ?>
                <?php echo theme('content') ?>
                </div>
<?php if ($sidebar): ?>
                    <div class="span4" id="sidebar">
                            <?php echo $sidebar?>
                    </div>
<?php endif; ?>
            </div>
            <div class="row">
                <div class="span12" id="footer">
        <?php echo theme('footer') ?>
                </div>
            </div>
        </div>
<?php echo theme('after') ?>
    </body>
</html>
