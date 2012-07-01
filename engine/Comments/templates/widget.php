<h2><?php echo t('Comments','Comments.widget')?></h2>
<?php foreach($comments as $comment):?>
    <?php echo $comment->render('widget')?>
<?php endforeach; ?>
<p class="fl_r"><a href="<?php echo l('/comments/')?>"><?php echo t('all comments &rarr;','Comments.widget')?></a></p>
