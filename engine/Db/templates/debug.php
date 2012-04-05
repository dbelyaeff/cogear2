<div>
<?= t('<b>Database queries:</b>','Database Debug')?><br/>
<? $summary = 0;
foreach($queries as $query=>$time) { ?>
    <div class="db-debug-query"><i><?= $query?></i> « <?= t('Time: ','Database Debug').$time?></div>
<? 
$summary += $time;
} ?>
<?= t('<b>Database Time:</b>','Database Debug').' '.$summary;?>
</div>