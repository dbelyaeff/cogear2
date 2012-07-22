<?php
 event('chat.msg.render',$item);
$author = user($item->aid);
?>
<div class="chat-msg shd">
    <div class="chat-msg-avatar">
        <?php echo $author->getLink('avatar') ?>
    </div>
    <div class="chat-msg-body">
        <div class="chat-msg-body-author">
            <?php echo $author->getLink('profile') ?>
        </div>
        <?php echo $item->body ?>
    </div>
    <div class="chat-msg-action">
        <?php if (access('Chat.msg', $item)): ?>
            <a href="/chat/msg/delete/<?php echo $item->id ?>" class="chat-action sh"><i class="icon icon-remove"></i></a>
        <? endif; ?>
    </div>
    <div class="chat-msg-time">
        <?php
        if (date('d') == date('d', $item->created_date)) {
            echo '<span title="' . df($item->created_date) . '">' . date('H:i:s', $item->created_date) . '</span>';
        } else {
            echo '<span title="' . date('H:i', $item->created_date) . '">' . date('d.m', $item->created_date) . '</span>';
        }
        ?>
    </div>

</div>