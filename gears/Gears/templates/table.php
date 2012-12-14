<form action="<?php echo $action ?>" method="POST">
    <input type="search" class="span3" placeholder="<?php echo t("Начните ввод для фильтрации…"); ?>" id="search-gears"/>
    <?php // echo template('Gears/templates/formaction')->render() ?>
    <table class="table table-bordered table-hover" id="gears-table">
        <thead>
            <tr>
               <!-- <th width="5%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked',this.checked)"></th> -->
                <th width="15%"><?php echo t('Название') ?></th>
                <th  width="10%"><?php echo t('Версия') ?></th>
                <th  width="45%"><?php echo t('Описание') ?></th>
                <th  width="20%"><?php echo t('Действия') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gears as $gear): ?>
                <tr  <?php if ($gear->status() > Gears::DISABLED): ?>class="success"<?php endif; ?>>
                   <!-- <td class="t_c"><input type="checkbox" name="gears[<?php echo $gear->gear; ?>]" <?php if ($gear->enabled OR $gear->status() == Gears::ENABLED): ?>checked="checked"<?php endif; ?> <?php if ($gear->enabled): ?>disabled="disabled"<?php endif; ?>/>
                    </td> -->
                    <td><?php echo $gear->name ?>
                        <?php if (method_exists($gear, 'admin') && ($gear->status() == Gears::ENABLED OR $gear->enabled)): ?>
                            <a href="<?php echo l('/admin/' . $gear->base); ?>" title="<?php echo t("Настройки"); ?>"><i class="icon-cog"></i></a>
                        <?php endif; ?>
                        <?php if (filectime($gear->dir) > time() - 3600 && $gear->status() != Gears::ENABLED): ?>
                            <span class="label label-success"><small><?php echo t('Новая'); ?></small></span>
                        <?php endif; ?>
                    </td>
                    <td><?php if ($gear->status() == Gears::CORE): ?>
                            <span  class="label"><?php echo t('Системная') ?></span>
                            <?php else:?>
                            <?php echo $gear->version ?>
                            <?php endif;?></td>
                    <td><?php echo $gear->description ?>
                        <p class="gear-info">
                            <b><?php echo t("Автор"); ?>:</b> <a href="<?php echo $gear->site; ?>"><?php echo $gear->author; ?></a>
                            <?php if ($gear->required): ?>
                                <br/>
                                <strong><?php echo t('Зависимости'); ?>:</strong>
                                <?php
                                $result = $gear->checkRequiredGears();
                                $output = new Core_ArrayObject();
                                foreach ($result->gears as $gear_name => $code) {
                                    if (TRUE === $code) {
                                        $output->append('<span class="label label-success">' . $gear_name . '</span>');
                                    } else {
                                        $output->append('<span class="label label-important">' . $gear_name. ($code ? ' ' . $code : '') . '</span>');
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
                        <?php endif; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <!-- <th width="5%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked',this.checked)"></th>-->
                <th width="10%"><?php echo t('Название') ?></th>
                <th  width="10%"><?php echo t('Версия') ?></th>
                <th  width="50%"><?php echo t('Описание') ?></th>
                <th  width="20%"><?php echo t('Действия') ?></th>
            </tr>
        </tfoot>
    </table>
    <?php /// echo template('Gears/templates/formaction')->render() ?>
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