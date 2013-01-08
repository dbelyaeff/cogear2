<div class="row">
    <?php
    $form = new Form(array(
                'name' => 'i18n.choose',
                'class' => 'form-horizontal',
                'elements' => array(
                    'gears' => array(
                        'type' => 'select',
                        'label' => t('Выберите шестерёнку: '),
                        'values' => array(),
                        'value' => $path,
                    ),
                    'themes' => array(
                        'type' => 'select',
                        'label' => t('Выберите тему: '),
                        'values' => array(),
                        'value' => $path,
                    ),
                )
            ));
    $gears = new Gears(GEARS, array(// Проверять ли на совместимость шестерёнки
                'check' => FALSE,
                // Удалять ли те, которые проверку не прошли
                'remove' => FALSE,
                // Сортировать ли по свойству конфига order
                'sort' => FALSE,
                // Превращать ли конфиги в объекты шестерёнок
                'charge' => TRUE));
    $values = array();
    foreach ($gears as $gear) {
        $values[ltrim($gear->folder, '/')] = $gear->name;
    }
    asort($values);
    $values = array(0 => '', 'gears' => t('Все шестерёнки')) + $values;
    $form->gears->setValues($values);
    $values = array();
    if ($themes = cogear()->theme->getThemes()) {
        foreach ($themes as $theme) {
            $values[ltrim($theme->folder, '/')] = $theme->name;
        }
    }
    $values = array(0 => '', 'themes' => t('Все темы')) + $values;
    $form->themes->setValues($values);
    echo $form->render();
    ?>
    <style>
        form#form-i18n-choose .control-group {
            float: left;
        }
        form#form-i18n-choose  {
            float: none;
            clear: both;
        }
    </style>
    <script>
        $base = "<?php echo $base; ?>";
        $(document).ready(function(){

            $('form select').change(function(){
                $this = $(this);
                $('#i18n-action-button').attr('data-action',$base + '/' + $this.val());
                $('form select').each(function(){
                    if($(this).attr('name') != $this.attr('name')){
                        $(this).val(0);
                    }
                })
            })
            if($('#i18n-action-button').attr('data-action') != $base){
                $('form select').filter(function(){
                    return $(this).val() != '';
                }).change();
            }
        })
    </script>
</div>