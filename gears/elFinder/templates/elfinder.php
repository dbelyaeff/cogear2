<script type="text/javascript">
    $().ready(function() {
        var elf = $('#elfinder').elfinder({
            lang: 'ru',             // language (OPTIONAL)
            url : '<?=Url::link('elfinder/connector')?>'  // connector URL (REQUIRED)
        }).elfinder('instance');			
    });
</script>
<div id="elfinder"></div>