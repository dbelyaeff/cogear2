<form action="<?php echo $action ?>" method="POST">
    <input type="search" class="span3" placeholder="<?php echo t("Type to filter…"); ?>" id="search-gears"/>
    <?php echo template('Gears/templates/formaction')->render() ?>
    <table class="table table-bordered table-hover" id="gears-table">
        <thead>
            <tr>
                <th width="5%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked',this.checked)"></th>
                <th width="15%"><?php echo t('Name') ?></th>
                <th  width="10%"><?php echo t('Version') ?></th>
                <th  width="45%"><?php echo t('Description') ?></th>
                <th  width="20%"><?php echo t('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gears as $gear): ?>
                <tr  <?php if ($gear->enabled OR $gear->status() == Gears::ENABLED): ?>class="success"<?php endif; ?>>
                    <td class="t_c"><input type="checkbox" name="gears[<?php echo $gear->gear; ?>]" <?php if ($gear->enabled OR $gear->status() == Gears::ENABLED): ?>checked="checked"<?php endif; ?> <?php if ($gear->enabled): ?>disabled="disabled"<?php endif; ?>/>
                    </td>
                    <td><?php echo t($gear->name, 'Gears') ?>
                        <?php if (method_exists($gear, 'admin') && ($gear->status() == Gears::ENABLED OR $gear->enabled)): ?>
                            <a href="<?php echo l('/admin/' . $gear->base); ?>" title="<?php echo t("Settings"); ?>"><i class="icon-cog"></i></a>
                        <?php endif; ?>
                        <?php if (filectime($gear->dir) > time() - 3600 && $gear->status() != Gears::ENABLED): ?>
                            <span class="label label-success"><small><?php echo t('New'); ?></small></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $gear->version ?></td>
                    <td><?php echo t($gear->description, 'Gears') ?>
                        <p class="gear-info">
                            <strong><?php echo t("Author", 'Gears'); ?>:</strong> <a href="<?php echo $gear->site; ?>"><?php echo $gear->author; ?></a>
                        <?php if ($gear->required): ?>
                            <br/>
                            <strong><?php echo t('Required', 'Gears'); ?>:</strong>
                            <?php
                            $result = $gear->checkRequiredGears();
                            $output = new Core_ArrayObject();
                            foreach ($result->gears as $gear_name => $code) {
                                if (TRUE === $code) {
                                    $output->append('<span class="label label-success">' . t($gear_name, 'Gears') . '</span>');
                                } else {
                                    $output->append('<span class="label label-important">' . t($gear_name, 'Gears') . ($code ? ' ' . $code : '').'</span>');
                                }
                            }
                            echo $output;
                            ?>
                        <?php endif; ?>
                        </p>
                    </td>
                    <td> <?php if ($gear->status() == Gears::EXISTS): ?>
                            <a href="<?php echo l(TRUE) . e(array('do' => 'install', 'gears' => $gear->gear)) ?>" class="btn btn-success btn-mini"> <?php echo t('Install') ?></a>
                        <?php endif; ?>
                        <?php if ($gear->status() == Gears::DISABLED): ?>
                            <a href="<?php echo l(TRUE) . e(array('do' => 'enable', 'gears' => $gear->gear)) ?>"  class="btn btn-primary btn-mini"><?php echo t('Enable') ?></a>
                        <?php endif; ?>
                        <?php if ($gear->status() == Gears::ENABLED): ?>
                            <a href="<?php echo l(TRUE) . e(array('do' => 'disable', 'gears' => $gear->gear)) ?>"  class="btn btn-mini"><?php echo t('Disable') ?></a>
                        <?php endif; ?>
                        <?php if ($gear->status() == Gears::DISABLED): ?>
                            <a href="<?php echo l(TRUE) . e(array('do' => 'uninstall', 'gears' => $gear->gear)) ?>" class="btn btn-danger btn-mini"><?php echo t('Uninstall') ?></a>
                        <?php endif; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th width="5%" class="t_c"><input type="checkbox" onclick="$('#gears-table tr:visible input[type=checkbox]:not(:disabled)').attr('checked',this.checked)"></th>
                <th width="10%"><?php echo t('Name') ?></th>
                <th  width="10%"><?php echo t('Version') ?></th>
                <th  width="50%"><?php echo t('Description') ?></th>
                <th  width="20%"><?php echo t('Actions') ?></th>
            </tr>
        </tfoot>
    </table>
    <?php echo template('Gears/templates/formaction')->render() ?>
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