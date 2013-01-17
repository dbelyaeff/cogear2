<div class="row">
    <?php
    $form = new Form(array(
                'name' => 'lang.choose',
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
                'charge' => FALSE));
    $values = array();
    foreach ($gears as $gear) {
        $values[ltrim('gears/'.$gear->gear, '/')] = $gear->name;
    }
    asort($values);
    $defaults = array(0 => '');
    if (!empty($option_all)) {
        $defaults['gears'] = t('Все шестерёнки');
    }
    $values = $defaults + $values;
    $form->gears->setValues($values);
    $values = array();
    if ($themes = cogear()->theme->getThemes()) {
        foreach ($themes as $theme) {
            $values[ltrim($theme->folder, '/')] = $theme->name;
        }
    }
    $defaults = array(0 => '');
    if (!empty($option_all)) {
        $defaults['themes'] = t('Все темы');
    }
    $values = $defaults + $values;
    $form->themes->setValues($values);
    echo $form->render();
    ?>
    <style>
        form#form-lang-choose .control-group {
            float: left;
        }
        form#form-lang-choose  {
            float: none;
            clear: both;
        }
    </style>
    <script type="text/javascript">
        base_uri = "<?php echo $base; ?>";
        $(document).ready(function(){
            $('form select').change(function(){
                $this = $(this);
                $('#lang-action-button').attr('data-action',base_uri + '/' + $this.val());
                $('form select').each(function(){
                    if($(this).attr('name') != $this.attr('name')){
                        $(this).val(0);
                    }
                })
            })
            if($('#lang-action-button').attr('data-action') != base_uri){
                $('form select').filter(function(){
                    return $(this).val() != '';
                }).change();
            }
        })
    </script>
</div>