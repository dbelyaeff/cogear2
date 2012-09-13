<?php
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;

if (!$item->teaser) {
    $before = new Stack(array('name' => 'page.show.full.before'));
    $before->object($item);
    echo $before->render();
}
?>
<div class="page shd">
    <div class="page-title">
        <?php
        $title = new Stack(array('name'=>'page.title'));
        $title->name = '<h2>' . ($item->teaser ? '<a href="' . $item->getLink() . '"><h2>' . $item->name . '</a>' : $item->name) ;
        if (!$item->preview) {
            if (access('page.edit.all') OR access('page.edit') && cogear()->user->id == $item->aid) {
                $title->name .= '<a href="' . $item->getLink('edit') . '" class="sh" title="'.t('Edit').'"><i class="icon-pencil"></i></a>';
            }
        }
        $title->name .= '</h2>';
        echo $title->render();
        ?>
    </div>
    <div class="page-body">
        <?php echo nl2br($item->body); ?>
    </div>
    <?php if (config('page.info.show')): ?>
        <div class="page-info">
            <?php
            $info = new Stack('page.info');
            config('page.info.time') && $info->time = icon('time') . ' ' . df($item->created_date);
            if (config('page.info.author')) {
                $info->author = $user->getAvatarImage('avatar.page');
                $info->author_link = $user->getLink('profile');
            }
            echo $info->render();
            ?>
        </div>
    <?php endif; ?>
</div>
<?php
if (!$item->teaser) {
    $after = new Stack(array('name' => 'page.show.full.after'));
    $after->object($item);
    echo $after->render();
}