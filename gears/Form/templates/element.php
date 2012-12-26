<div class="control-group <? if($required){?>required<?}?><? if(isset($class)){echo ' '.$class;}?>" id="<?php echo $form->getId()?>-<?php echo $name?>">
<? if($label){?><label class="control-label" for="<?php echo $name?>"><?php echo $label?><? if($required){?> *<?}?></label><?}?>
<div class="controls"><?php echo $code?>
<?php if($errors = $element->getErrors()):?><p class="help-inline"><?php echo $errors->toString('<br/>')?></p><?php endif;?>
<? if($description){?><div class="description" id="<?php echo $element->getId()?>-description"><?php echo $description?></div><?}?>
</div>
</div>
