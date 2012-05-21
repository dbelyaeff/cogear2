<?php
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;
if (!$item->published && !$item->preview) {
    $item->class = $item->class ? $item->class . ' hidden' : 'hidden';
}
if ($item->by_post_author) {
    $item->class = $item->class ? $item->class . ' post-author' : 'post-author';
}

?>
<div class="comment <?php
if ($item->class) {
    echo $item->class;
}
?> shd l<?php if(!$item->flat) echo $item->level; ?>" data-level="<?php echo $item->level ?>" data-id="<?php echo $item->id ?>" id="comment-<?php echo $item->id ?>">
    <div class="comment-info">
        <?php
        event('comment.render', $item);
        $info = new Stack(array('name' => 'comment.info'));
        $info->attach($item);
        $info->author = '<span class="comment-author">' . $user->getAvatarImage() . ' ' . $user->getLink('profile') . '</span>';
        access('Comments.ip') && $info->ip = '<span class="comment-ip">' . $item->ip . '</span>';
        $info->anchor = icon('time') . '<a class="ms5 scrollTo hltr" href="#comment-' . $item->id . '">' . df($item->created_date) . '</a>';
        if($item->post){
            $info->post = '<i class="icon-edit"></i> <a href="'.$item->post->getLink().'#comment-'.$item->id.'">'.$item->post->name.'</a>';
        }
        if (!$item->preview) {
            if ($item->pid) {
                $info->parent = '<a class="ms5 scrollTo hltr" href="#comment-' . $item->pid . '">&uarr;</a>';
            }
            if (access('Comments.delete', $item)) {
                $info->delete = '<a class="comment-delete sh" data-id="' . $item->id . '" href="' . $item->getLink('delete') . '"><i class="icon-remove"></i></a>';
            }
            if (access('Comments.hide', $item)) {
                $info->hide = '<a class="comment-hide sh" data-id="' . $item->id . '" href="' . $item->getLink('hide') . '"><i class="icon-eye-' . ($item->published ? 'close' : 'open') . '"></i></a>';
            }
            if (access('Comments.edit', $item)) {
                $info->edit = '<a class="comment-edit sh" data-id="' . $item->id . '" href="' . $item->getLink('edit') . '"><i class="icon-pencil"></i></a>';
            }
        }
        echo $info->render();
        ?>
    </div>
    <div class="comment-body">
        <?php echo $item->body ?>
    </div>
    <div class="comment-action">
        <?php
        if (!$item->preview) {
            $action = new Stack(array('name' => 'comment.action'));

            if (access('Comments.reply')) {
                $action->reply = '<a class="btn btn-mini" data-type="reply" data-target="comment-' . $item->id . '" href="' . l('/comments/reply/' . $item->id) . '">' . t('Reply', 'Comment') . '</a>';
            }
            echo $action->render();
        }
        ?>
    </div>
</div>