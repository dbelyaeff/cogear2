<?php
$base = l('/admin/lang/ajax/scan/');
echo template('I18n/templates/choose', array('path' => $path, 'base' => $base));
?>
<p class="t_c">
    <button class="btn btn-primary btn-large" id="i18n-action-button" data-action="<?php echo $base . $path ?>"><?php echo t("Начать сканирование") ?></button>
    <button class="btn" id="i18n-scan-reset"><?php echo t("Сброс") ?></button>
</p>
<div class="well" id="i18n-logger" style="display: none;"></div>
<div class="t_c" id="hud">
    <div id="result"></div>
    <div id="progress"></div>
    <div id="counter"></div>
</div>
<p class="alert alert-info"><?php echo t('Внимание! В режим сканирования отдельный категории (только шестерёнки или только темы) происходит обновление языковых файлов всех сканируемых шестерёнок и тем!'); ?></p>
<div class="t_c" id="actions" style="display: none;">
    <a href="<?php echo l('/admin/lang/download/index') ?>" class="btn btn-primary btn-large"><?php echo t('Скачать') ?></a>
    <a href="<?php echo l('/admin/lang/use/index') ?>" class="btn btn-success btn-large"><?php echo t('Использовать') ?></a>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#i18n-scan-reset').on('click',function(){
            $('#hud').hide();
            $('#actions').slideUp();
            $('#i18n-logger').html('');
            $('#i18n-action-button').one('click',function(){
                cogear.ajax.loader.type('blue-stripe');
                cogear.ajax.loader.el.appendTo($('#progress'));
                cogear.ajax.loader.show();
                $('#hud').slideDown();
                $('#i18n-logger').logger($(this).attr('data-action'),{reset: true});
            })
        })
        $('#i18n-logger').on('loggerReply',function(event,data){
            $('#result').html(data.result);
            $('#hud').hasClass('thumbnail') || $('#hud').addClass('thumbnail');
            $('#counter').html('<b>' + data.index + '</b>' + '/' + data.total + ' <i class="icon icon-search"></i> ' + data.strings);
        })
        $('#i18n-logger').on('loggerStop',function(event,data){
            $('#hud').slideUp();
            if(data.finish){
                $('#actions').slideDown();
            }
        });
        $('#i18n-scan-reset').click();
    });
</script>