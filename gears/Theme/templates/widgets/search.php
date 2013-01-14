<form class="form form-horizontal">
    <div class="input-append">
    <input type="search" name="uri" value="<?php echo cogear()->input->get('uri','')?>" class="input-xxlarge" placeholder="<?php echo t('Введите путь страницы, виджеты на которой вы хотите увидеть');?>"/>
    <button type="submit" class="btn btn-primary"><?php echo icon('eye-open icon-white')?></button>
    </div>
</form>