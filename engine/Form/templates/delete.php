<span class="delete <?php echo $options->class?>">
<input type="button" value="<?php echo $options->value?>" class="btn btn-danger" onclick="$(this).hide();$(this).next().show();"/>
<span class="btn-group delete-confirm">
<input type="submit" name="<?php echo $options->name?>" value="<?php echo t('Yes')?>" class="btn btn-danger"/>
<input type="button" value="<?php echo t('No')?>" class="btn btn-primary" onclick="$(this).parent().hide();$(this).parent().prev().show();"/>
</span>
</span>