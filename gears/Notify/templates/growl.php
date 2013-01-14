<script type="text/javascript">
    $(document).ready(function(){
<?php
$options = array();
if ($title)
    $options['header'] = addslashes($title);
if ($class)
    $options['theme'] = $class;
$options['position'] = config('notify.growl.position', 'top-right');
?>
            $.jGrowl("<?php echo addslashes($body) ?>"<?php if ($options) {
    echo ', ' . json_encode($options);
} ?>)
        });
</script>