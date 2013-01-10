<?php
echo $before;
?>
<div class="control-group <?php if ($required) { ?>required<?php } ?><?php
if (isset($class)) {
    echo ' ' . $class;
}
?>" id="<?php echo $form->getId() ?>-<?php echo $name ?>">
        <?php if ($label) { ?><label class="control-label" for="<?php echo $name ?>"><?php echo $label ?><?php if ($required) { ?> *<?php } ?></label><?php } ?>
    <div class="controls"><?php echo $code ?>
<?php if ($errors = $element->getErrors()): ?><p class="help-inline"><?php echo $errors->toString('<br/>') ?></p><?php endif; ?>
<?php if ($description) { ?><div class="description" id="<?php echo $element->getId() ?>-description"><?php echo $description ?></div><?php } ?>

    </div>
</div>
<?php
echo $after;
?>
