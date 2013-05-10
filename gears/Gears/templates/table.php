<form action="<?php echo l('/admin/gears/status/') ?>" method="POST">
    <div class="input-append"><input type="search" class="input-xxlarge" placeholder="<?php echo t("Начните ввод для фильтрации…"); ?>" id="search-gears" tabindex="0"/><a href="#search-gears" class="btn" onclick="$('#search-gears').val('<?php echo t('Настройки') ?>').trigger('change')"><i class="icon icon-cogs"></i></a> <a href="#search-gears" class="btn btn-primary" onclick="$('#search-gears').val('<?php echo t('Обновить') ?>').trigger('change')"><i class="icon icon-refresh"></i></a></div>
    <?php echo template('Gears/templates/formaction', array('do' => 'do'))->render() ?>
    <table class="table table-bordered table-hover" id="gears-table">
        <thead>
            <tr>
                <th width="3%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked', this.checked)"></th>
                <th width="15%"><?php echo t('Название') ?></th>
                <th  width="45%"><?php echo t('Описание') ?></th>
                <th  width="20%"><?php echo t('Действия') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($gears as $key=>$gear): ?>
            <?php if($key == 'Highslide'){

            }?>
                <?php
                switch ($gear->status()) {
                    case Gears::CORE:
                        $class = 'info';
                        break;
                    case Gears::ENABLED:
                        $class = 'success';
                        break;
                    case Gears::DISABLED:
                        $class = '';
                        break;
                }
                ?>
                <tr  class="<?php echo $class ?>" id="<?php echo $gear->gear; ?>">
                    <td class="t_c"><input type="checkbox" name="gears[]" value="<?php echo $gear->gear; ?>" <?php if ($gear->status() == Gears::CORE): ?>disabled="disabled"<?php endif; ?>/>
                    </td>
                    <td>
                        <?php echo $gear->info('name') ?>
                        <?php if (method_exists($gear, 'admin_action') && $gear->status() == Gears::ENABLED): ?>
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
                                foreach ($gear->required->gears as $info) {
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
                            <a href="<?php echo l('/admin/gears/status') . e(array('do' => 'enable', 'gears' => $gear->gear)) ?>"  class="btn btn-success btn-mini"><?php echo t('Включить') ?></a>
                        <?php elseif ($gear->status() == Gears::ENABLED): ?>
                            <a href="<?php echo l('/admin/gears/status') . e(array('do' => 'disable', 'gears' => $gear->gear)) ?>"  class="btn btn-mini btn-danger"><?php echo t('Выключить') ?></a>
                        <?php endif; ?>
                        <?php if (method_exists($gear, 'admin_action') && $gear->status() != Gears::DISABLED): ?>
                            <a href="<?php echo l('/admin/' . strtolower($gear->gear)) ?>"  class="btn btn-mini"><?php echo t('Настройки') ?></a>
                        <?php endif; ?>
                        <?php if (TRUE === $gear->checkUpdate()): ?>
                            <a href="<?php echo l('/admin/gears/update/' . strtolower($gear->gear)) ?>"  class="btn btn-mini btn-primary"><?php echo t('Обновить') ?></a>
                        <?php endif; ?>
                        <?php if ($gear->status() !== Gears::CORE): ?>
                            <a href="<?php echo l('/admin/gears/download') . '?gears=' . $gear->gear ?>" class="btn btn-mini" title="<?php echo t('Скачать') ?>"><?php echo icon('download') ?></a>                      <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <th width="3%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked', this.checked)"></th>
                <th width="10%"><?php echo t('Название') ?></th>
                <th  width="50%"><?php echo t('Описание') ?></th>
                <th  width="20%"><?php echo t('Действия') ?></th>
            </tr>
        </tfoot>

    </table>
    <?php echo template('Gears/templates/formaction', array('do' => 'do-alt'))->render() ?>
</form>
<script>
        $(document).ready(function() {
            $('#search-gears').focus();
        })
        function filterTable(value) {
            if (value) {
                $('#gears-table tbody tr:not(:contains("' + value + '"))').hide();
                $('#gears-table tbody  tr:contains("' + value + '")').show();
            }
            else {
                $('#gears-table tbody tr').show();
            }
        }
        $('#search-gears').bind({
            keyup: function() {
                filterTable($(this).val());
            },
            change: function() {
                filterTable($(this).val());
            },
            clearFields: function() {
                filterTable($(this).val());
            }
        })
</script>