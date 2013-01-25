<?php
event('parse', $item);
if ($item->show_breadcrumb) {
    $elements = array();
    if ($parents = $item->getParents()) {
        foreach ($parents as $parent) {
            $elements[] = array(
                'label' => $parent->name,
                'link' => $parent->getLink(),
            );
        }
    }
    $elements[] = array(
        'label' => $item->name,
        'link' => $item->getLink(),
    );
    $bc = new Breadcrumb(array(
        'name' => 'page',
        'render' => FALSE,
        'multiple' => TRUE,
        'elements' => $elements,
            ));
    echo $bc->render();
} else {
    if($item->show_title){
        title($item->name);
    }
}
?>
<article class="page shd posrel">
<?php if (access('Pages.admin')): ?>
        <a class="sh posabs topright" href="<?php echo $item->getLink('edit') ?>"><?php echo icon('pencil') ?></a>
    <?php endif ?>
    <?php if ($item->show_title): ?><div class="page-header"><h1><?php echo $item->name ?></h1></div><?php endif ?>
    <div class="page-body">
    <?php echo $item->body ?>
    </div>
</article>