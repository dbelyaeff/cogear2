<?php
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;

if (!$item->teaser) {
    $before = new Stack(array('name' => 'post.show.full.before'));
    $before->attach($item);
    echo $before->render();
}
?>
<div class="post">
    <div class="post-title">
        <?php
        $title = new Stack('post.title');
        $title->name = '<h2>' . ($item->teaser ? '<a href="' . $item->getLink() . '"><h2>' . $item->name . '</a>' : $item->name) . '</h2>';
        if (!$item->preview) {
            if (access('post.edit.all') OR access('post.edit') && cogear()->user->id == $item->aid) {
                $title->edit = '<a href="' . l('/post/edit/' . $item->id) . '" class="btn ' . ($item->published ? 'btn-primary ' : ' ') . 'btn-mini">' . t($item->published ? 'Edit' : 'Draft', 'Post') . '</a>';
            }
        }
        echo $title->render();
        ?>
    </div>
    <div class="post-body">
        <?php echo $item->body ?>
    </div>
    <div class="post-info">
        <?php
        $info = new Stack('post.info');
        $info->time = icon('time') . ' ' . df($item->created_date);
        $info->author = $user->getAvatarImage('avatar.post');
        $info->author_link = $user->getProfileLink();
        echo $info->render();
        ?>
    </div>
</div>
<?php
if (!$item->teaser) {
    $after = new Stack(array('name' => 'post.show.full.after'));
    $after->attach($item);
    echo $after->render();
}