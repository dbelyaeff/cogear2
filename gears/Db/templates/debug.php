<div class="well">
<b><?php echo  t('Database queries','Db.Debug')?></b> (<?php echo $data->count()?>):<br/>
<? $summary = 0;
$i = 1;
foreach($data as $item) { ?>
    <div class="db-debug-query"><?php echo $i?>. <i><?php echo  $item->query?></i> « <?php echo  t('Time: ','Database Debug').''.$item->time?></div>
<?
$i++;
$summary += $item->time;
} ?>
<?php echo  t('<b>Database Time:</b>','Database Debug').' '.$summary;?>
</div>