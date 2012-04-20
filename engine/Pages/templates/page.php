<?php
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;

if (!$item->teaser) {
    $before = new Stack(array('name' => 'page.show.full.before'));
    $before->attach($item);
    echo $before->render();
}
?>
<div class="page">
    <div class="page-title">
        <?php
        $title = new Stack('page.title');
        $title->name = '<h2>' . ($item->teaser ? '<a href="' . $item->getLink() . '"><h2>' . $item->name . '</a>' : $item->name) . '</h2>';
        if (!$item->preview) {
            if (access('page.edit.all') OR access('page.edit') && cogear()->user->id == $item->aid) {
                $title->edit = '<a href="' . $item->getEditLink() . '" class="btn ' . ($item->published ? 'btn-primary ' : ' ') . 'btn-mini">' . t($item->published ? 'Edit' : 'Draft', 'Post') . '</a>';
            }
        }
        echo $title->render();
        ?>
    </div>
    <div class="page-body">
        <?php echo $item->body ?>
    </div>
    <?php if (config('page.info.show')): ?>
        <div class="page-info">
            <?php
            $info = new Stack('page.info');
            config('page.info.time') && $info->time = icon('time') . ' ' . df($item->created_date);
            if (config('page.info.author')) {
                $info->author = $user->getAvatarImage('avatar.page');
                $info->author_link = $user->getProfileLink();
            }
            echo $info->render();
            ?>
        </div>
    <?php endif; ?>
</div>
<?php
if (!$item->teaser) {
    $after = new Stack(array('name' => 'page.show.full.after'));
    $after->attach($item);
    echo $after->render();
}