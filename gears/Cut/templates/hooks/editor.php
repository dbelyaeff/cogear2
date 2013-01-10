<a class="btn" id="cut-button">
    <?php echo icon('eye-close') . ' ' . t("Кат"); ?>
</a>
<script>
    $(document).ready(function(){
        $('#cut-button').on('click',function(){
            window.insertWysiwyg("\n[cut]");
        })
    })
</script>
