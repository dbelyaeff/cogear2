<div>
<?php echo  t('<b>Database queries:</b>','Database Debug')?><br/>
<? $summary = 0;
foreach($queries as $query=>$time) { ?>
    <div class="db-debug-query"><i><?php echo  $query?></i> « <?php echo  t('Time: ','Database Debug').$time?></div>
<? 
$summary += $time;
} ?>
<?php echo  t('<b>Database Time:</b>','Database Debug').' '.$summary;?>
</div>