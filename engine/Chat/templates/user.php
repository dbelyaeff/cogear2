<span id="chat-user-<?php echo $user->id ?>">
    <?php echo $user->getLink('avatar') ?>
    <?php
    if (access('Chat.admin', $chat)) {
        echo ' <span><a href="/chat/leave/' . $chat->id . '/' . $user->id . '" class="chat-action" title="' . t('Kick off', 'Chat.widget') . '"><i class="icon icon-remove"></i></a></span>';
    }
    ?>
</span>