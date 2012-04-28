<script type="text/javascript">
    <?php
        $options = array();
        if($title) $options['header'] = addslashes ($title);
        if($class) $options['theme'] = $class;
        $options['position'] = config('notify.growl.position','bottom-right');
    ?>
    $.jGrowl("<?php echo addslashes($body)?>"<?php if($options){ echo ', '.json_encode($options);}?>)
</script>