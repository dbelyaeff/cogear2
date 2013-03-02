<ol class="page-list">
    <?php $last_page = NULL; ?>
    <?php foreach ($pages as $page): ?>
        <?php
        if ($last_page) {
            if (strlen($page->thread) > strlen($last_page->thread) && strpos($page->thread, $last_page->thread) === 0) {
                echo '<ol>';
            } else if (strlen($page->thread) < strlen($last_page->thread)) {
                echo str_repeat('</ol>', $last_page->level - $page->level);
            } else {
                echo '</li>';
            }
        } else {
            echo '</li>';
        }
        ?>
        <li class="dd-page" id="page-<?php echo $page->id ?>"><a href="<?php echo $page->getLink() ?>"><?php echo $page->name ?></a>

            <?php
            $last_page = $page;
        endforeach;
        ?>
</ol>
