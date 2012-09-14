<?php
if ($element->options->multiple) {
    $name = $element->options->name . '[]';
} else {
    $name = $element->options->name;
}
?>
<input id="<?php echo $element->id ?>" type="file" name="<?php echo $name ?>" <?php if($element->options->ajax):?>data-url="<?php echo $element->options->ajax ?>"<?php endif;?> <?php
if ($element->options->multiple) {
    echo ' multiple';
}
?>>
<div class="attached-files"></div>
<?php if($element->options->ajax):?>
<script>
    $(document).ready(function(){
        $el = $('#<?php echo $element->id ?>');
        $el.fileupload({
            dataType: 'json',
            done: function (e, response) {
                data = response.result;
                if(data.success){
                    $new = $(data.code);
                    $new.appendTo($('#<?php echo $element->getId(); ?> .attached-files').last());
                }
            }
        });
    });
</script>

<?php endif;?>