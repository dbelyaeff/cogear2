<?php
event('parser', $item);
$elements = array();
if($parents = $item->getParents()){
    foreach($parents as $parent){
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
    'elements' => $elements,
));
echo $bc->render();
?>
<article class="page">
    <div class="page-header"><h1><?php echo $item->name ?></h1></div>
    <div class="page-body">
        <?php echo $item->body ?>
    </div>
</article>