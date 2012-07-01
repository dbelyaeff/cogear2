<h2><?php echo t('Top blogs','Blog.widget')?></h2>
<table>
    <thead>
        <tr>
            <td></td>
            <td></td>
            <td><b><?php echo t('Readers', 'Blogs') ?></b></td>
            <td><b><?php echo t('Posts', 'Blogs') ?></b></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($blogs as $blog): ?>
            <tr>
                <td width="10%"><?php echo $blog->getLink('avatar'); ?></td>
                <td  align="center" width="50%"><?php echo $blog->getLink('profile'); ?></td>
                <td align="center"><a href="<?php echo $blog->getLink('users'); ?>"><?php echo $blog->object->followers ?></a></td>
                <td align="center"><a href="<?php echo $blog->getLink(); ?>"><?php echo $blog->posts ?></a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td align="right" colspan="4"><a href="<?php echo l('/blogs') ?>"><?php echo t('all blogs &rarr;', 'Blogs.widget') ?></a></td>
        </tr>
    </tfoot>
</table>