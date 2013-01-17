<div id="menus">
    <?php foreach ($menus as $menu): ?>
        <div class="dd-container shd" data-id="<?php echo $menu->id ?>">
            <a class="sh"><i class="icon icon-move"></i></a>
            <?php echo $menu->name; ?>
            <a class="sh fl_r" title="<?php echo t('Редактировать') ?>" href="<?php echo l('/admin/theme/menu/' . $menu->id) ?>"><i class="icon icon-pencil"></i></a>
            <a class="sh fl_r" title="<?php echo t('Список пунктов') ?>" href="<?php echo l('/admin/theme/menu/' . $menu->id . '/items') ?>"><i class="icon icon-list"></i></a>
            <a class="sh fl_r" title="<?php echo t('Добавить пункт') ?>" href="<?php echo l('/admin/theme/menu/' . $menu->id . '/item/add') ?>"><i class="icon icon-plus"></i></a>
        </div>
    <?php endforeach; ?>
</div>
<script>
    $(document).ready(function(){
        $('#menus').sortable({
            handle: '.icon-move',
            placeholder: 'drop-placeholder',
            dropOnEmpty: true,
            stop: function(event,ui){
                data = {menus:{}};
                $('.dd-container').each(function(index,el){
                    data.menus[$(el).attr('data-id')] = index;
                })
                $.ajax({
                    url: '<?php echo l('/admin/theme/menu/ajax/order') ?>',
                    data: data,
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function(){
                        cogear.ajax.loader.type('black-spinner top-right').after($('#content')).show();
                    },
                    complete: function(){
                        cogear.ajax.loader.hide();
                    }
                })
            }
        }).disableSelection();
    })
</script>
