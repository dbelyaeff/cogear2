<div id="widgets-list">
    <?php foreach ($regions as $region): ?>
        <section>
            <h1><span><?php echo t('Регион')?></span> <?php echo $region ?></h1>
            <div class="region-widgets shd">
                <?php foreach ($widgets->filter('region', $region) as $widget): ?>
                    <div class="region-widget">
                        <a class="sh"><i class="icon icon-move"></i></a>
                        <span class="region-widget-name">
                            <?php echo $widget->name ?>
                        </span>
                        <a href="<?php echo l('/admin/theme/widgets/'.$widget->id)?>" class="sh fl_r" title="<?php echo t("Редактировать")?>"><i class="icon icon-pencil"></i></a>
                        <a href="<?php echo l('/admin/theme/widgets/'.$widget->id).'/options'?>" class="sh fl_r" title="<?php echo t("Настройки")?>"><i class="icon icon-wrench"></i></a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
<style>
    #widgets-list section{
        border: 1px solid #CCC;
        padding: 0;
        margin: 10px 0;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -o-border-radius: 5px;
        border-radius: 5px;
    }
    #widgets-list section h1{
        font-size: 1.2em;
        margin: 0px;
        padding: 0 0 0 14px;
        background-color: #f1f1f1;
        background-image: -ms-linear-gradient(top,#f9f9f9,#ececec);
        background-image: -moz-linear-gradient(top,#f9f9f9,#ececec);
        background-image: -o-linear-gradient(top,#f9f9f9,#ececec);
        background-image: -webkit-gradient(linear,left top,left bottom,from(#f9f9f9),to(#ececec));
        background-image: -webkit-linear-gradient(top,#f9f9f9,#ececec);
        background-image: linear-gradient(top,#f9f9f9,#ececec);
        text-shadow: 1px 1px 1px #FFF;
    }
    #widgets-list section h1 span{
        color: rgb(185, 185, 185);
        text-shadow: 1px 1px 0px #FFF;
    }
    #widgets-list section div.region-widgets{
        background: #FEFEFE;
        min-height: 50px;
        padding: 5px 10px;
    }

    .region-widget {
        padding: 10px;
        background: #fafafa;
        background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
        background:    -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
        background:         linear-gradient(top, #fafafa 0%, #eee 100%);
        border: 1px solid #CCC;
        border-radius: 5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        -o-border-radius: 5px;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .icon-move {
        cursor: move;
    }
    .sh.fl_r{
        margin-left: 10px;
    }
</style>