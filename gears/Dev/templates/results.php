<div class="well">
    <?php event('dev.trace');?>
    <?php
    $bench = bench();
    $data = humanize_bench($bench['done']);
    ?>
    <p>
    <?php echo t('<b>Система:</b> ').icon('time').' '.round($data['time'],3).' '.icon('asterisk').$data['memory']?>
</div>

