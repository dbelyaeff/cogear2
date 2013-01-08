<h2><?php echo t('Лучшие пользователи')?></h2>
<table>
    <thead>
        <tr>
            <td></td>
            <td></td>
            <td align="center"><b><?php echo t('Рейтинг') ?></b></td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td width="10%"><?php echo $user->getLink('avatar'); ?></td>
                <td  align="center" width="50%"><?php echo $user->getLink('profile'); ?></td>
                <td align="center"><?php echo $user->Рейтинг ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td align="right" colspan="3"><a href="<?php echo l('/users') ?>"><?php echo t('все пользователи &rarr;') ?></a></td>
        </tr>
    </tfoot>
</table>