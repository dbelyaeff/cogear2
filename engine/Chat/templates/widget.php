<h3><?php echo t('Controls', 'Chat.widget') ?></h3>
<b><?php echo t('Chat admin:', 'Chat.widget') ?></b> <?php echo user($chat->aid)->getLink('avatar') ?>
<?php if (access('Chat.admin', $chat)): ?>
    <form id="form-chat-invite" method="POST" action="<?php echo l('/chat/invite/'.$chat->id)?>">
        <div class="control-group m5">
            <div class="controls">
                <div class="input-append">
                    <input class="span3 autocomplete" name="users" data-source="<?php echo l('/user/autocomplete/') ?>" size="16" type="text"><input type="submit" class="btn btn-primary" value="<?php echo t('Invite', 'Chat.widget') ?>"/>
                </div>
            </div>
        </div>
    </form>
<? endif; ?>
<div id="chat-users-container">
    <b><?php echo t('Users:','Chat.widget')?></b>
    <?php
    $users = $chat->getUsers();
    foreach ($users as $uid) {
        if ($user = user($uid)) {
            echo template('Chat.user', array('user' => $user, 'chat' => $chat))->render();
        }
    }
    ?>
</div>
<?php if (access('Chat.admin', $chat)): ?>
<p align="right"><a href="<?php echo l('/chat/delete/'.$chat->id);?>" class="chat-action noajax btn btn-danger"><?php echo t('Delete chat','Chat.widget')?></a></p>
<?php endif; ?>
