<form action="<?php echo $action ?>" method="POST">
    <input type="search" class="span3" placeholder="<?php echo t("Начните ввод для фильтрации…"); ?>" id="search-gears"/>
    <?php echo template('Gears/templates/formaction')->render() ?>
    <table class="table table-bordered table-hover" id="gears-table">
        <thead>
            <tr>
               <th width="3%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked',this.checked)"></th>
                <th width="15%"><?php echo t('Название') ?></th>
                <th  width="45%"><?php echo t('Описание') ?></th>
                <th  width="20%"><?php echo t('Действия') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gears as $gear): ?>
                <tr  <?php if ($gear->status() > Gears::DISABLED): ?>class="success"<?php endif; ?> id="<?php echo $gear->gear; ?>">
                   <td class="t_c"><input type="checkbox" name="gears[<?php echo $gear->gear; ?>]" <?php if ($gear->status() == Gears::CORE): ?>disabled="disabled"<?php endif; ?>/>
                    </td>
                    <td><?php echo $gear->name ?>
                        <?php if (method_exists($gear, 'admin') && $gear->status() == Gears::ENABLED): ?>
                            <a href="<?php echo l('/admin/' . $gear->base); ?>" title="<?php echo t("Настройки"); ?>"><i class="icon-cog"></i></a>
                        <?php endif; ?>
                        <br/><span class="label label-info" title="<?php echo t('Версия: %s', $gear->version) ?>"><?php echo $gear->gear ?></span>
                        <?php if (filectime($gear->dir) > time() - 3600): ?>
                            <span class="label label-success"><small><?php echo t('Новая'); ?></small></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $gear->description ?>
                        <p class="gear-info">
                            <b><?php echo t("Автор"); ?>:</b> <a href="<?php echo $gear->site; ?>"><?php echo $gear->author; ?></a>
                            <?php
                            if ($gear->required):
                                $output = new Core_ArrayObject();
                                ?>
                                <br/>
                                <strong><?php echo t('Зависимости'); ?>:</strong>
                                <?php
                                foreach ($gear->required->gear as $info) {
                                    if (TRUE === $info->success) {
                                        if ($info->disabled) {
                                            $output->append('<span class="label label-inverse" title="' . t('Требование выполнено (шестерёнка отключена)') . '">' . $info->name . ($info->version ? ' ' . $info->version : '') . '</span>');
                                        } else {
                                            $output->append('<span class="label label-success" title="' . t('Требование выполнено') . '">' . $info->name . ($info->version ? ' ' . $info->version : '') . '</span>');
                                        }
                                    } else if (-2 == $info->success) {
                                        $output->append('<span class="label label-warning" title="' . t('Несовместимость') . '">' . $info->name . '</span>');
                                    } else {
                                        $output->append('<span class="label label-important" title="' . t('Требуется') . '">' . $info->name . ($info->version ? ' ' . $info->version : '') . '</span>');
                                    }
                                }
                                echo $output;
                                ?>
                            <?php endif; ?>
                        </p>
                    </td>
                    <td>

                        <?php if ($gear->status() == Gears::DISABLED): ?>
                            <a href="<?php echo l(TRUE) . e(array('do' => 'enable', 'gears' => $gear->gear)) ?>"  class="btn btn-success btn-mini"><?php echo t('Включить') ?></a>
                        <?php elseif ($gear->status() == Gears::ENABLED): ?>
                            <a href="<?php echo l(TRUE) . e(array('do' => 'disable', 'gears' => $gear->gear)) ?>"  class="btn btn-mini btn-danger"><?php echo t('Выключить') ?></a>
                        <?php endif; ?>
                        <?php if (method_exists($gear, 'admin') && $gear->status() != Gears::DISABLED): ?>
                            <a href="<?php echo l('/admin/'.strtolower($gear->gear)) ?>"  class="btn btn-mini"><?php echo t('Настройки') ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

          <tfoot>
          <tr>
          <th width="3%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked',this.checked)"></th>
          <th width="10%"><?php echo t('Название') ?></th>
          <th  width="50%"><?php echo t('Описание') ?></th>
          <th  width="20%"><?php echo t('Действия') ?></th>
          </tr>
          </tfoot>

    </table>
    <?php echo template('Gears/templates/formaction')->render()  ?>
</form>
<script>
    function filterTable(value){
        if(value){
            $('#gears-table tbody tr:not(:contains("'+value+'"))').hide();
            $('#gears-table tbody  tr:contains("'+value+'")').show();
        }
        else {
            $('#gears-table tbody tr').show();
        }
    }
    $('#search-gears').bind({
        keyup: function(){
            filterTable($(this).val());
        },
        change: function(){
            filterTable($(this).val());
        },
        clearFields: function(){
            filterTable($(this).val());
        }
    })
</script>