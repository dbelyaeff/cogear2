<div class="fl_r">
    <span class="span3">
        <select name="<?php echo $do?>" class="span3" data-placeholder="<?php echo t('Выберите действие…') ?>">
            <option></option>
            <option value="enable"><?php echo t('Включить'); ?></option>
            <option value="disable"><?php echo t('Выключить'); ?></option>
            <option value="download"><?php echo t('Скачать'); ?></option>
        </select>
    </span>
    <input type="submit" class="btn btn-mini" style="margin: 3px 0 0 6px;" value="<?php echo t('Применить') ?>"/>
</div>