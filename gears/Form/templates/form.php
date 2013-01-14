<?php
if ($options->title) {
    ?>
    <div class="page-header"><h2><?php echo $options->title; ?></h2></div>
    <?php
}
echo HTML::open_tag('form', $options) . "\n";
foreach ($form->elements as $element) {
    if ($element->render)
        echo $element->render() . "\n";
}
echo HTML::close_tag('form') . "\n";
