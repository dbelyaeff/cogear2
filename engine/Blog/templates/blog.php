<?php
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;

$before = new Stack(array('name' => 'community.show.full.before'));
$before->attach($item);
echo $before->render();
?>
<div class="community">
    <div class="community-title">
        <?php
        $title = new Stack('community.title');
        $title->avatar = $item->getAvatar();
        $title->name = '<h2>' . ($item->teaser ? '<a href="' . $item->getLink() . '"><h2>' . $item->name . '</a>' : $item->name) . '</h2>';
        if (access('community.edit.all') OR access('community.edit') && cogear()->user->id == $item->aid) {
            $title->edit = '<a href="' . $item->getEditLink() . '" class="btn btn-primary btn-mini">' . t('Edit') . '</a>';
        }
        $status = cogear()->community->status();
        switch($status){
            case 0:
                $title->join = '<a href="'.l('/community/status/'.$item->id).'" class="btn btn-success btn-mini">'.t('Join','Community').'</a>';
                break;
            case 1:
                $title->join = '<a href="'.l('/community/status/'.$item->id).'" class="btn btn-warning btn-mini">'.t('Leave','Community').'</a>';
                break;
            case 2:
                $title->join = '<a href="'.l('/community/status/'.$item->id).'" class="btn btn-danger btn-mini">'.t('Leave','Community').'</a>';
                break;
        }
        echo $title->render();
        ?>
    </div>
</div>
<?php
$after = new Stack(array('name' => 'community.show.full.after'));
$after->attach($item);
echo $after->render();