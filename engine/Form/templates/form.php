<?php
echo HTML::open_tag('form',$form)."\n";
foreach($elements as $element){
    if($element->render) echo $element->render()."\n";
}
echo HTML::close_tag('form')."\n";
?>
