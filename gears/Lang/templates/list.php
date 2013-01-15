<table class="table table-bordered table-hover" id="langs">
    <thead>
        <tr>
            <th width="30%"><?php echo t('Название языка') ?></th>
            <th width="15%"><?php echo t('Буквенный код') ?></th>
            <th width="15%"><?php echo t('По умолчанию') ?></th>
            <th width="30%"><?php echo t('Действия') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($langs as $code => $lang) {
            ?>
            <tr data-lang="<?php echo $code ?>">
                <td> <b><?php echo $lang ?></b></td>


                <td class="t_c"><?php echo $code ?></td>
                <td class="t_c shd setDefault"><?php if ($code == config('lang.lang')): ?><i class="icon icon-ok"></i><?php else: ?>
                        <a class="sh"><i class="icon icon-ok"></i></a>
                    <?php endif ?></td>
                <td>
                    <a class="btn btn-primary btn-mini <?php
                if ($code != config('lang.lang')) {
                    echo 'hidden';
                }
                    ?>" href="<?php echo l('/admin/lang/translate') ?>"><?php echo icon('list icon-white') . ' ' . t('Перевод') ?></a>
                    <a class="btn btn-mini <?php
                   if ($code != config('lang.lang')) {
                       echo 'hidden';
                   }
                    ?>" href="<?php echo l('/admin/lang/scan') ?>"><?php echo icon('list') . ' ' . t('Сканирование') ?></a>
                    <a class="delete btn btn-danger btn-mini <?php
                   if ($code == config('lang.lang')) {
                       echo 'hidden';
                   }
                    ?>" href="<?php echo l('/admin/lang/delete/' . $code) ?>"><?php echo icon('trash icon-white') . ' ' . t('Удалить') ?></a></td>
                    <?
                }
                ?>
    </tbody>
</table>
<script>
    $(document).ready(function(){
        $('#langs').on('click','.setDefault a',function(event){
            $this = $(this);
            $.ajax({
                url: l('/admin/lang/ajax/change'),
                type: 'POST',
                dataType: 'json',
                data: {
                    lang: $this.parents('tr').first().attr('data-lang')
                },
                beforeSend: function(){
                    $this.removeClass('sh').find('i').removeClass('icon-ok').addClass('icon-time');
                },
                success: function(data) {
                    if(data.success){
                        $this.removeClass('icon-time').addClass('icon-ok');
                        $('#langs tbody tr').each(function(){
                            if($(this).attr('data-lang') != $this.parents('tr').first().attr('data-lang')){
                                $(this).find('.setDefault').html('<a class="sh"><i class="icon icon-ok"></i></a>');
                                $(this).find('.btn').addClass('hidden');
                                $(this).find('.delete').removeClass('hidden');
                            }
                            else {
                                $(this).find('.setDefault').html('<i class="icon icon-ok"></i>');
                                $(this).find('.btn').removeClass('hidden');
                                $(this).find('.delete').addClass('hidden');
                            }
                        });
                    }
                }
            });
        })
    });
</script>