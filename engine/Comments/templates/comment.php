<?php
$user = new User();
$user->id = $item->aid;
$user->find();
$item->author = $user;
?>
<div class="comment l<?php echo $item->level-- ?>" id="comment-<?php echo $item->id ?>">
    <div class="comment-info">
        <?php
        $info = new Stack(array('name' => 'comment.info'));
        $info->author = $user->getAvatarImage();
        $info->author_link = $user->getProfileLink();
        $info->time = icon('time') . ' ' . df($item->created_date);
        if (!$item->preview) {
            $info->anchor = '<a class="ms5" data-highlight="#comment-' . $item->id . '" href="#comment-' . $item->id . '">#</a>';
            if ($item->pid) {
                $info->parent = '<a class="ms5" data-highlight="#comment-' . $item->pid . '" href="#comment-' . $item->pid . '">&uarr;</a>';
            }
            if (access('comment.edit.all') OR access('comment.edit') && cogear()->user->id == $item->aid) {
                $info->edit = '<a  href="' . $item->getEditLink() . '" class="btn btn-primary btn-mini">' . t('Edit', 'Post') . '</a>';
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
        $action = new Stack(array('name' => 'comment.action'));

        if (!$item->fronzen && $item->level < config('comments.max_level', 7)) {
            $action->reply = '<a class="btn btn-primary btn-mini" data-type="modal" data-source="form-comment" data-width="900" data-height="500" href="' . l('/comments/reply/' . $item->id) . '">' . t('Reply', 'Comment') . '</a>';
        }
        echo $action->render();
        ?>
    </div>
</div>