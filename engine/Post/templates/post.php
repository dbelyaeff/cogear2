<?php
event('post.render', $item);
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;

if (!($item->teaser OR $item->preview)) {
    $before = new Stack(array('name' => 'post.full.before'));
    $before->attach($item);
    echo $before->render();
}
if (!$item->published && !$item->preview) {
    $item->class = $item->class ? $item->class . ' draft' : 'draft';
}
?>
<div class="post <?php echo $item->class ?> shd" id="post-<?php echo $item->id ?>">
    <div class="post-title">
        <?php
        $title = new Stack(array('name' => 'post.title'));
        $title->attach($item);
        $title->name = '<h2>' . ($item->teaser ? '<a href="' . $item->getLink() . '"><h2>' . $item->name . '</a>' : $item->name) . '</h2>';
        if (!$item->preview) {
            if (access('Post.delete', $item)) {
                $title->delete = '<a class="post-delete sh" data-id="' . $item->id . '" href="' . $item->getLink('delete') . '"><i class="icon-remove"></i></a>';
            }
            if (access('Post.hide', $item)) {
                $title->hide = '<a class="post-hide sh" data-id="' . $item->id . '" href="' . $item->getLink('hide') . '"><i class="icon-eye-' . ($item->published ? 'open' : 'close') . '"></i></a>';
            }
            if (access('Post.edit', $item)) {
                $title->edit = '<a class="post-edit sh" data-id="' . $item->id . '" href="' . $item->getLink('edit') . '"><i class="icon-pencil"></i></a>';
            }
        }
        echo $title->render();
        ?>
    </div>
    <?php
    $before = new Stack(array('name' => 'post.before'));
    $before->attach($item);
    echo '<div class="post-before">' . $before->render() . '</div>';
    ?>
    <div class="post-body">
        <?php echo nl2br($item->body); ?>
    </div>
    <?php
    $after = new Stack(array('name' => 'post.after'));
    $after->attach($item);
    echo '<div class="post-before">' . $after->render() . '</div>';
    ?>
    <div class="post-info">
        <?php
        $info = new Stack(array('name' => 'post.info'));
        $info->attach($item);
        $info->time = '<span class="post-time">' . df($item->created_date) . '</span>';
        $info->author = $user->getLink('avatar', 'avatar.post');
        $info->author_link = $user->getLink('profile');
        echo $info->render();
        ?>
    </div>
</div>
<?php
if (!($item->teaser OR $item->preview)) {
    $after = new Stack(array('name' => 'post.full.after'));
    $after->attach($item);
    echo $after->render();
}