<?php if ($value && file_exists(UPLOADS.$value)): ?>
<div class="image-preview"><img src="<?php echo File::pathToUri($element->options->preset ? image_preset($element->options->preset, UPLOADS.$value) : UPLOADS.$value) ?>"></div>
<label class="checkbox"><input type="checkbox" name="<?php echo $element->name ?>" value=""/> <?php echo t('Удалить') ?></label>
<?php endif; ?>
<?php echo HTML::input($element->options) ?>

