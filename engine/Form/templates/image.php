<?if($value):?>
<div class="image-preview"><?=HTML::img($value,'',$image)?><a href="<?=Ajax::link(array(
    'action' => 'replace',
    'form' => $form->name,
    'element' => $element->name,
))?>" class="form-action delete">x</a></div>
<?endif;?>
<?=HTML::input($attributes)?>
