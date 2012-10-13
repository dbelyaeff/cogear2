<h2><?php echo t('Search', 'Search.widget') ?></h2>
<?php echo template('Search/templates/form',array('action'=>l('/search')))->render()?>