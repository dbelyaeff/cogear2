<?=HTML::input($attributes)?>
<?if($value):?>
<div class="file-preview"><?=HTML::a($value,$value)?> <a href="<?=Ajax::link(array(
    'action' => 'replace',
    'form' => $form->name,
    'element' => $element->name,
))?>" class="form-action delete">x</a></div>
<?endif;?>
