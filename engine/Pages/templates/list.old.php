<table class="table table-bordered">
    <thead>
    <tr>
    <th><?php echo t('Name'); ?></th>
    <th width="5%"><?php echo t('Views'); ?></th>
    </tr>
</thead>
<tbody>
    <?php foreach ($pages as $page): ?>
        <tr>
            <td class="l<?php echo $page->level?>"><a href="<?php echo $page->getEditLink() ?>"><?php echo $page->name ?></a>
                <a href="<?php echo $page->getEditLink() ?>" class="btn btn-primary btn-mini"><?php echo t('Edit'); ?></a>
                <?php echo icon('eye-'.($page->published ? 'open' : 'close'));?>
            </td>
            <td><?php echo $page->views ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>