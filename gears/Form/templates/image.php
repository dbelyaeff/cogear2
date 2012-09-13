<?php if ($value && file_exists(UPLOADS.$value)): ?>
<div class="image-preview"><img src="<?php echo File::pathToUri(UPLOADS.$value) ?>"></div>
<label class="checkbox"><input type="checkbox" name="<?php echo $element->name ?>" value=""/> <?php echo t('Delete') ?></label>    
<?php endif; ?>
<?php echo HTML::input($element->options) ?>

