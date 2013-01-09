<?php
if ($element->options->multiple) {
    $name = $element->options->name . '[]';
} else {
    $name = $element->options->name;
}
?>
<input id="<?php echo $element->id ?>" type="file" name="<?php echo $name ?>" <?php
if ($element->options->multiple) {
    echo ' multiple';
}
?>>
