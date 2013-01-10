<a class="btn" id="cut-button">
    <?php echo icon('eye-close') . ' ' . t("Кат"); ?>
</a>
<script>
    $(document).ready(function(){
        $('#cut-button').on('click',function(){
            if(0 == $('form textarea:contains("[cut"),form div:contains("[cut")').length){
                window.insertWysiwyg("\n[cut]<br/>");
            }
        })
    })
</script>
