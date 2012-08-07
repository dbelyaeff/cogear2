<form action="<?php echo l('/tags/').(isset($action) ? '/'.$action : '');?>" method="GET" id="form-tags-search">
    <div class="control-group">
        <div class="controls">
            <div class="input-append">
               <input name="tags" type="text" class="span7 autocomplete" data-source="<?php echo l('/tags/autocomplete/')?>" class="span3" <?php if(isset($value) && $value){ echo 'value="'.$value.'"';}?>placeholder="<?php echo t("Type to search…", 'Tags') ?>"/><button class="btn" type="submit"><i class="icon icon-search"></i></button>
            </div>
        </div>
    </div>
</form>