<?php
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;

$before = new Stack(array('name' => 'blog.before'));
$before->attach($item);
echo $before->render();
?>
<div class="blog">
    <div class="blog-title">
        <?php
        $title = new Stack('blog.title');
        $title->avatar = $item->getAvatar();
        $title->name = '<h2>' . ($item->teaser ? '<a href="' . $item->getLink() . '"><h2>' . $item->name . '</a>' : $item->name) . '</h2>';
        if (access('Blog.edit.all') OR access('Blog.edit') && cogear()->user->id == $item->aid) {
            $title->edit = '<a href="' . $item->getLink('edit') . '"><i class="icon-pencil"></i></a>';
        }
        $status = cogear()->blog->status();
        switch($status){
            case 0:
                $title->join = '<a href="'.l('/blog/status/'.$item->id).'" class="btn btn-success btn-mini">'.t('Join','blog').'</a>';
                break;
            case 1:
                $title->join = '<a href="'.l('/blog/status/'.$item->id).'" class="btn btn-warning btn-mini">'.t('Leave','blog').'</a>';
                break;
            case 2:
                $title->join = '<a href="'.l('/blog/status/'.$item->id).'" class="btn btn-danger btn-mini">'.t('Leave','blog').'</a>';
                break;
        }
        echo $title->render();
        ?>
    </div>
</div>
<?php
$after = new Stack(array('name' => 'blog.after'));
$after->attach($item);
echo $after->render();