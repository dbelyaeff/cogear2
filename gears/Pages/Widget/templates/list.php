<div class="pages-list">
    <?php foreach ($pages as $page): ?>
        <div data-level="<?php echo $page-level?>"><?php echo HTML::a($page->getLink(),$page->name)?></div>
    <?php endforeach; ?>
</div>