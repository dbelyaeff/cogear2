<?php
$base = l('/admin/lang/ajax/scan/');
echo template('Lang/templates/choose', array('path' => $path, 'base' => $base, 'option_all' => TRUE));
?>
<p class="t_c">
    <button class="btn btn-primary btn-large" id="lang-action-button" data-action="<?php echo $base . $path ?>"><?php echo t("Начать сканирование") ?></button>
    <button class="btn" id="lang-scan-reset"><?php echo t("Сброс") ?></button>
</p>
<div class="well" id="lang-logger" style="display: none;"></div>
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
        $('#lang-scan-reset').on('click',function(){
            $('#hud').hide();
            $('#hud div').html('');
            $('#actions').slideUp();
            $('#lang-logger').html('');
            $('#lang-action-button').one('click',function(){
                cogear.ajax.loader.type('blue-stripe');
                cogear.ajax.loader.el.appendTo($('#progress'));
                cogear.ajax.loader.show();
                $('#hud').slideDown();
                $('#lang-logger').logger($(this).attr('data-action'),{reset: true});
            })
        })
        $('#lang-logger').on('loggerReply',function(event,data){
            $('#result').html(data.result);
            $('#hud').hasClass('thumbnail') || $('#hud').addClass('thumbnail');
            $('#counter').html('<b>' + data.index + '</b>' + '/' + data.total + ' <i class="icon icon-search"></i> ' + data.strings);
        })
        $('#lang-logger').on('loggerStop',function(event,data){
            $('#hud').slideUp();
            if(data.finish){
                $('#actions').slideDown();
            }
        });
        $('#lang-scan-reset').click();
    });
</script>