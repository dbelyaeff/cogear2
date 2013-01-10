<div class="snippet">
    <div class="snippet-name">
        <?php echo $item->name?>
    </div>
    <?php if ($item->aid == user()->id OR access('Code.snippet', $item)): ?>
        <a class="sh edit" href="<?php echo l('/admin/code/snippet/' . $item->id); ?>"><i class="icon icon-pencil"></i></a>
    <?php endif; ?>
        <pre class="prettyprint linenums shd"><?php echo htmlspecialchars($item->code) ?></pre>
</div>