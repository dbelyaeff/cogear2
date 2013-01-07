<div class="alert alert-info t_c">
    <p><?php echo t('Страница еще не создана, но вы можете её создать прямо по этому адресу.');?></p>
    <p><a class="btn btn-primary" href="<?php echo l('/admin/pages/create/').'?uri='.cogear()->router->getUri()?>"><?php echo t('Создать страницу')?></a></p>
</div>