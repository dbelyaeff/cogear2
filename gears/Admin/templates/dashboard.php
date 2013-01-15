<div id="dashboard">
    <div class="row-fluid">
        <?php $row = 0; ?>
        <?php foreach ($panels as $panel): ?>
            <div class="panel span<?php echo $panel->span ?>">
                <div class="panel-header"><?php echo $panel->title ?></div>
                <div class="panel-body">
                    <?php echo $panel->content ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<style>
    #dashboard {
        margin: 0 0 30px 0;
    }
    div.panel-header{
        padding: 5px 10px;
        font-weight: bold;
        text-shadow: 1px 1px 0px #FFF;
        background: #fafafa;
        background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
        background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
        background: linear-gradient(top, #fafafa 0%, #eee 100%);
        border: 1px solid #CCC;
        border-bottom: 1px solid #FEFEFE;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        -webkit-border-top-left-radius: 5px;
        -webkit-border-top-right-radius: 5px;
        -moz-border-top-left-radius: 5px;
        -moz-border-top-right-radius: 5px;
    }
    div.panel-body{
        padding: 5px;
        border: 1px solid #CCC;
        border-top: none;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        -webkit-border-bottom-left-radius: 5px;
        -webkit-border-bottom-right-radius: 5px;
        -moz-border-radius-bottomleft: 5px;
        -moz-border-radius-bottomright: 5px;
    }
</style>
