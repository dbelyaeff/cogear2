<?php foreach($pages as $page):?>
<div data-level="<?php echo $page->level?>"><a href="<?php echo $page->getLink()?>"><?php echo $page->name?></a></div>
<?php endforeach; ?>
