<?php
$default = cogear()->theme->object();

$screenshot = $default->getScreenshot();
$info = getimagesize($screenshot);
$thumbnail = File::pathToUri($screenshot);
?>


<div class="page-header">
    <h1><?php echo $default->name ?> <small><?php echo t('Текущая тема оформления') ?></small></h1>
</div>
<div class="row theme">
    <div class="span4">
        <a href="#" class="thumbnail"><img src="<?php echo $thumbnail ?>" <?php echo $info[3] ?>/></a>
    </div>
    <div class="span7">
        <div class="well"><?php echo $default->description ?></div>
        <p><b><?php echo t('Версия: ') . '</b> ' . $default->version ?> <br/><b><?php echo t('Автор: ') . '</b> ' . HTML::a('mailto:' . $default->email, $default->author) ?><br/> <b><?php echo t('Сайт: ') . '</b> ' . HTML::a($default->site, $default->site) ?>
                        <?php echo $default->menu ?>
                        </div>
                        </div>
                        <div class="page-header">
                            <h2><?php echo t('Выбрать тему') ?></h2>
                        </div>
                        <?php
                        if (!$themes) {
                            echo template('Errors/templates/empty')->render();
                        } else {
                            ?>
                            <ul class="thumbnails">
                                        <?php foreach ($themes as $theme):
                                            if($theme->theme == $default->theme){
                                                continue;
                                            }
                                            ?>
                                    <li class="span4">
                                        <div class="thumbnail">
                                            <?php
                                            $screenshot = $theme->getScreenshot();
                                            $info = getimagesize($screenshot);
                                            $thumbnail = File::pathToUri($screenshot);
                                            ?>
                                            <a target="_blank" href="<?php echo l() . '?theme=' . $theme->theme ?>" class="thumbnail" title="<?php echo t('Посмотреть') ?>"><img src="<?php echo $thumbnail ?>" <?php echo $info[3] ?>/></a>
                                            <div class="caption">
                                                <h3><?php echo $theme->name ?></h3>
                                                <p><?php echo $theme->description ?></p>
                                                <p><a href="<?php echo l('/admin/theme/activate/' . $theme->theme) ?>" class="btn btn-primary"><?php echo t('Активировать') ?></a> <a href="<?php echo l() . '?theme=' . $theme->theme ?>" target="_blank" class="btn"><?php echo t('Посмотреть') ?></a> <?php echo $theme->menu ?></p>

                                            </div>
                                        </div>
                                    </li>
                            <?php endforeach; ?>
                            </ul>
<?php } ?>
                        <style>
                            .thumbnail .menu {
                                margin: 0;
                                padding: 0;
                                display: inline;
                            }
                        </style>