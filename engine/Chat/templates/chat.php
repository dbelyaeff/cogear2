<div class="chat" id="chat-<?php echo $chat->id ?>" data-id="<?php echo $chat->id ?>">
    <div class="chat-window"  id="chat-window-<?php echo $chat->id ?>" >
        <?php foreach ($chat->getMessages() as $msg): ?>
            <?php echo $msg->render(); ?>
        <?php endforeach; ?>
    </div>
</div>