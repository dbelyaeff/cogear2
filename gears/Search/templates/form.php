<form action="<?php echo $action; ?>" method="GET">
    <div class="control-group">
        <div class="controls">
            <div class="input-append">
                <input name="q" type="text" class="span3" <?php
    echo 'value="' . cogear()->input->get('q','') . '"';
?>placeholder="<?php echo t("Наберите для поиска…") ?>"/><button class="btn" type="submit"><i class="icon icon-search"></i></button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $tables = $('.table-searchable');
        if($tables.length){
            $('[name=q]').on('keydown keyup change',function(){
                value = $(this).val();
                if(value){
                    $tables.find('tbody tr:not(:contains("'+value+'"))').hide();
                    $tables.find('tbody tr:contains("'+value+'")').show();
                }
                else {
                    $tables.find('tbody tr').show();
                }
            })
        }
    })
</script>