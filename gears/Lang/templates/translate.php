<?php
$base = l('/admin/lang/translate/');
echo template('Lang/templates/choose', array('path' => $path, 'base' => $base))
?>
<?php if (!Lang::factory('index')->object()->count()): ?>
    <div class="alert alert-info">
        <?php echo t('Индекс файла с перевода ещё не создан. Нажмите на ссылку справа в меню выше.'); ?>
    </div>
<?php endif; ?>
<div class="row">
    <form class="form-search fl_l">
        <div class="input-prepend">
            <span class="add-on"><i class="icon icon-search"></i></span>
            <input type="text" id="lang-search" name="q" class="input-xxlarge" placeholder="<?php echo t("Введите строку для поиска…") ?>" value="<?php echo cogear()->input->get('q', ''); ?>">
        </div>
        <button type="submit" class="btn"><?php echo t('Найти') ?></button>
    </form>
    <button class="btn btn-mini fl_r" id="lang-reset" style="margin-top: 3px;"><?php echo t("Сбросить") ?></button>
    <button class="btn btn-primary fl_r" style="margin-right: 5px;" id="lang-action-button" data-action="<?php echo $base . $path ?>"><?php echo t("Фильтровать") ?></button>
</div>
<script>
    $('#lang-search').on('keyup',function(){
        $this = $(this);
        if($this.val()){
            $('#translations .control-group:not(:contains("'+$this.val().replace('"','\"')+'"))').hide();
            $('#translations .control-group:contains("'+$this.val().replace('"','\"')+'")').show();
        }
        else {
            $('#translations .control-group').show();
        }
    })
</script>
<div id="translations-wrapper">
    <div id="translations" style="position:relative;">
        <div class="btn-group" style="position: fixed; top: 50%; left: 10%;" >
            <button class="btn btn-mini" id="fillAll" title="<?php echo t('Заполнить все') ?>"><i class="icon icon-arrow-down"></i></button>
            <button class="btn btn-mini" id="saveAll" title="<?php echo t('Сохранить все') ?>"><i class="icon icon-pencil"></i></button>
        </div>
        <form>
            <?php
            if ($file) {
                $options = config('lang');
                if($lang = cogear()->input->get('lang')){
                    $options->lang = $lang;
                }
                $options->file = $file;
                $index = Lang::factory('temp', $options);
                $index->load();
            } else {
                $index = Lang::factory('index');
            }
            $i = 0;
            foreach ($index->object() as $key => $value) {
                ?>
                <div class="control-group">
                    <label class="control-label"><?php echo htmlspecialchars($key); ?> <button class="btn btn-mini sync"><?php echo icon('arrow-down'); ?></button></label>
                    <div class="controls">
                        <div class="input-append">
                            <input id="<?php echo md5($key) ?>" type="text" tabindex="<?php echo++$i; ?>" class="input-block-level" data-source="<?php echo htmlspecialchars($key); ?>" placeholder="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value) ?>">
                            <button class="btn edit" type="button"><?php echo icon('pencil') ?></button>
                        </div>
                    </div>
                </div>
                <?
            }
            ?>
        </form>
    </div>
</div>
<script>
    function setExtraButtons(){
        $('#fillAll').on('click',function(event){
            $('#translations-wrapper').find('button.sync').trigger('click');
            return false;
        })
        $('#saveAll').on('click',function(event){
            $values = {}
            $('#translations-wrapper').find('input').each(function(){
                $i = $(this);
                if($i.val()){
                    $i = $(this);
                    $values[$i.attr('data-source')] = $i.val();
                }
            });
            $.ajax({
                url: '<?php echo l('/admin/lang/ajax/save/') ?>',
                type: 'post',
                dataType: 'json',
                data: {
                    values:  $values,
                    path: cogear.ajax.path
                },
                beforeSend: function(){
                    $('button.edit').attr('disabled','disabled');
                    $('button.edit').find('i').toggleClass(false).addClass('icon-time');
                },
                complete: function(){
                    $('button.edit').find('i').toggleClass(false).addClass('icon-ok');
                    setTimeout(function(){
                        $('button.edit').find('i').toggleClass(false).addClass('icon-pencil');
                        $('button.edit').removeAttr('disabled');
                    }, 1000);
                }
            })
            return false;
        })
    }
    $(document).ready(function(){
        $base = '<?php echo $base ?>';
        cogear.ajax.path = '<?php echo $path; ?>';
        $('form select').change(function(){
            cogear.ajax.path = $(this).val();
        })
        $('#lang-reset').on('click',function(event){
            event.preventDefault();
            $(this).next().attr('data-action','<?php echo l('/admin/lang/translate/') ?>')
            $(this).next().click();
            return false;
        })
        $('#lang-action-button').on('click',function(event){
            $('<p class="t_c" id="ajax-holder"></p>').insertBefore($('#translations-wrapper'));
            cogear.ajax.loader.type('blue-stripe');
            cogear.ajax.loader.el.prependTo('#ajax-holder').show();
            $('#translations-wrapper').load($(this).attr('data-action')+' #translations',function(){
                cogear.ajax.loader.el.hide();
                setExtraButtons();
            });
        });
        setExtraButtons();
        $('#translations-wrapper').on('click','button.sync',function(event){
            event.preventDefault();
            event.stopPropagation();
            $input = $(this).parent().next().find('input');
            $input.val($input.attr('data-source'));
            return false;
        })
        $('#translations-wrapper').on('click','button.edit',function(event){
            event.preventDefault();
            event.stopPropagation();
            $this = $(this);
            $input = $this.prev('input');
            if(!$input.val()) return;
            $this.attr('disabled','disabled');
            $.ajax({
                url: '<?php echo l('/admin/lang/ajax/save/') ?>',
                type: 'post',
                dataType: 'json',
                data: {
                    'source': $input.attr('data-source'),
                    'translation': $input.val(),
                    'path':  cogear.ajax.path
                },
                beforeSend: function(){
                    $this.find('i').toggleClass(false).addClass('icon-time');
                },
                complete: function(){
                    $this.find('i').toggleClass(false).addClass('icon-ok');
                    setTimeout(function(){
                        $this.find('i').toggleClass(false).addClass('icon-pencil');
                        $this.removeAttr('disabled');
                    }, 1000);
                }
            })
            return false;
        })
        $('#lang-search').trigger('keyup');
    });
</script>
<style>
    .ajax-loader.black-spinner{
        margin-top: 7px;
    }
</style>