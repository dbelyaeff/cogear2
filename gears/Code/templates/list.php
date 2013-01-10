<table class="table table-bordered table-hover table-searchable">
    <thead>
        <tr>
            <th>№</th>
            <th><?php echo t('Название сниппета') ?></th>
            <th><?php echo t('Синтаксис') ?></th>
            <th><?php echo t('Автор') ?></th>
            <th><?php echo t('Создан') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($snippets as $snippet): ?>
            <tr class="shd">
                <td><?php echo $snippet->id ?></td>
                <td class="sh"><?php echo HTML::a(l('/admin/code/snippet/' . $snippet->id), $snippet->name) ?> <?php echo HTML::a(l('/admin/code/snippet/' . $snippet->id), icon('pencil')) ?>
                    <?php if (cogear()->input->get('splash') === ''): ?>
                        <button class="btn fl_r btn-mini insert" data-id="<?php echo $snippet->id ?>"><?php echo t('Вставить') ?></button>
                    <?php endif; ?>
                </td>
                <td><?php echo $snippet->type ?></td>
                <td><?php
                $author = user($snippet->aid);
                echo $author->getLink('avatar') . ' ' . $author->getLink('profile');
                    ?></td>
                <td><?php echo df($snippet->object()->created_date) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th>№</th>
            <th><?php echo t('Название сниппета') ?></th>
            <th><?php echo t('Синтаксис') ?></th>
            <th><?php echo t('Автор') ?></th>
            <th><?php echo t('Создан') ?></th>
        </tr>
    </tfoot>
</table>
<script>
    $(document).ready(function(){
<?php if (cogear()->input->get('splash') === ''): ?>
            $('.insert').on('click',function(){
                window.top.insertWysiwyg("\n<p>[code snippet="+$(this).attr('data-id')+"]</p>\n",'textarea');
                $(window.top.document).find('.fancybox-close').click();
            })
<?php endif ?>
    });
</script>