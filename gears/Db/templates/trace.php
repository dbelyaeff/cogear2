<?php
    $points = bench();
    // Специально задаем нулевой элемент, потому что счёт ведется от единицы
    $db_bench = array();
    $total_time = 0;
    $memory = 0;
    foreach($points as $key=>$point){
        if(0 === strpos($key,'db.query')){
            $memory += $point['memory'];
            $point = humanize_bench($point);
            $db_bench[] = $point;
            $total_time += $point['time'];
        }
    }
?>
<?php echo t('<b>База данных:</b> %d <i class="icon icon-time"></i> %.3f<i class="icon icon-asterisk"></i>%s', $queries->count(),$total_time,File::fromBytes($memory)); ?> <?php if($queries->count()):?><a href="#db-trace-queries" id="db-trace-queries-handler" class="btn btn-mini"><i class="icon icon-eye-open"></i></a>
<div id="db-trace-queries" class="well" style="display:none;">
    <?php foreach ($queries as $key=>$query): ?>
        <?php echo icon('time').' '. round($db_bench[$key]['time'],4).' '.icon('asterisk').' '.$db_bench[$key]['memory']?>
        <?php echo '<code class="prettyprint lang-sql">'.$query.'</code>'?>
    <br/>
    <?php endforeach; ?>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#db-trace-queries-handler').on('click',function(){
            $('#db-trace-queries').css('display') == 'none' ? $('#db-trace-queries').slideDown() : $('#db-trace-queries').slideUp();
        });
    })
</script>
<?php endif;?>
