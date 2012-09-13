<div class="well">
<?php 
 $dev = new Stack(array('name'=>'dev.info'));
 $dev->append(t('<b>Generated in:</b> %.3f (second|seconds)','Dev',$data['time']));
 $dev->append(t('<b>Memory consumption:</b> %s','Dev',$data['memory']));
 echo $dev->render('<br/>');
?>
</div>

