<h2><?php echo t('Search', 'Search.widget') ?></h2>
<?php echo template('Search.form',array('action'=>l('/search')))->render()?>