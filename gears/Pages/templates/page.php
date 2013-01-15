<?php
event('parser', $item);
?>
<article class="page">
<h1><?php echo $item->name ?></h1>
<div class="page-body">
    <?php echo $item->body?>
</div>
</article>