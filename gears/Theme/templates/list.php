<?php
$theme = cogear()->theme->object();

$screenshot = $theme->getScreenshot();
$info = getimagesize($screenshot);
$thumbnail = File::pathToUri($screenshot);
?>


<div class="page-header">
    <h1><?php echo $theme->name ?> <small><?php echo t('Текущяя тема оформления') ?></small></h1>
</div>
<div class="row">
    <div class="span4">
        <a href="#" class="thumbnail"><img src="<?php echo $thumbnail ?>" <?php echo $info[3] ?>/></a>
    </div>
    <div class="span7">
        <div class="well"><?php echo $theme->description ?></div>
        <p><b><?php echo t('Версия: '). '</b> '.$theme->version?> <br/><b><?php echo t('Автор: '). '</b> '.HTML::a('mailto:'.$theme->email,$theme->author)?><br/> <b><?php echo t('Сайт: '). '</b> '.HTML::a($theme->site,$theme->site)?>
    </div>
</div>
<div class="page-header">
    <h2><?php echo t('Выбрать тему') ?></h2>
</div>
    <?php if(!$themes){
       echo template('Errors/templates/empty')->render();
    } else {?>
<ul class="thumbnails">
    <?php foreach ($themes as $theme): ?>
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
                    <p><a href="<?php echo l('/admin/theme/activate/' . $theme->theme) ?>" class="btn btn-primary"><?php echo t('Активировать') ?></a> <a href="<?php echo l() . '?theme=' . $theme->theme ?>" target="_blank" class="btn"><?php echo t('Посмотреть') ?></a></p>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<?php } ?>