<p class="alert alert-info">
    <?php echo t('На данной страницы вы можете выгрузить настройки сайта или же загрузть их. <br/>Помните, что все ваши текущие настройки <b>будут удалены</b> и заменены на новые!')?>
</p>
<p class="t_c">
    <a href="<?php echo l('/admin/site/export')?>" class="btn"><?php echo icon('download').' '.t('Скачать')?></a>
</p>