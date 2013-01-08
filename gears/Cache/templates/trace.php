<?php
    $caches = array();
    foreach(array('system','normal') as $cache){
        $caches[$cache] = Cache::factory($cache)->stats;
    }
?>
<?php echo '<br/>'.t('<b>Кэшировние:</b> ');
foreach($caches as $name=>$results){
    echo ''.$name.' <span title="'.t('Чтение/Запись').'">('.$results->read.'/'.$results->write.')</span> ';
}