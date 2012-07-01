<form action="<?php echo l('/search').(isset($action) ? '/'.$action : '');?>" method="GET">
    <div class="control-group">
        <div class="controls">
            <div class="input-append">
               <input name="q" type="text" class="span3" <?php if(isset($value) && $value){ echo 'value="'.$value.'"';}?>placeholder="<?php echo t("Type to search…", 'Search.widget') ?>"/><button class="btn" type="submit"><i class="icon icon-search"></i></button>
            </div>
        </div>
    </div>
</form>