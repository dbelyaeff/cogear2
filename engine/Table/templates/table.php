<table class="<?php echo $class ?>" id="table-<?php echo $table?>">
    <thead>
        <?php foreach ($fields as $field): ?>
            <th<?php if($field->width):?> width="<?php echo $field->width;?>"<?php endif;?>><?php echo $field->label?></th>
        <?php endforeach; ?>
    </thead>
    <tbody>
        <?php foreach($items as $key=>$item):?>
            <tr id="table-<?php echo $table.'-'.$key?>">
                <?php foreach($item as $row):?>
                    <td class="<?php echo $row->class?>"><?php echo $row->value;?></td>
                <?php endforeach;?>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>