<?php
echo HTML::open_tag('form',$options)."\n";
foreach($form->elements as $element){
    if($element->render) echo $element->render()."\n";
}
echo HTML::close_tag('form')."\n";
?>
