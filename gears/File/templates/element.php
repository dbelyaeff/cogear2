<?php
if ($value) {
    $path = File::pathToUri($value);
    echo icon('download').' '.HTML::a($path, $path);
    ?>
    <label class="checkbox"><input type="checkbox" name="<?php echo $element->name ?>" value=""/> <?php echo t('Удалить') ?></label>

    <?
} else {
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
       <?php } ?>
