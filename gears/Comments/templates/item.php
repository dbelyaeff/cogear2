<? $user = new User_Object($item->aid)?>
<div class="comment" id="comment-<?=$item->id?>" <?if($item->level > 1):?>style="margin-left:<?=$item->level*config('comments.margin-left','15')?>px"<?endif?>>
    <div class="comment-info">
        <? $comment_info = new Stack('comment.info');
           $comment_info->avatar = $user->getAvatarLinked();;
           $comment_info->author = $user->getLink();
           $comment_info->time = '<span class="time">'.icon('time').' '.df($item->created_date).'</span>';
           if($item->reply){
               $comment_info->reply = HTML::a('#comment-'.$item->reply,'↑',array('class'=>'in-reply-to','rel'=>$item->id));
           }
           echo $comment_info;
           ?>
    </div>
    <div class="comment-body"><?=$item->body?></div>
    <div class="comments-control">
                   <?php
                   $control = new Stack('comments.control');
                   $control->reply = HTML::a('#comment-'.$item->id,t('reply'),array('class'=>'comment-reply','rel'=>$item->id));
                   if(access('comments edit_all') OR access('comments edit') && $item->id == $cogear->user->id){
                       $control->edit = HTML::a('#/comments/edit/'.$item->id,t('edit'));
                   }
                   if(access('comments delete_all') OR access('comments delete') && $item->id == $cogear->user->id){
                       $control->delete = HTML::a('#/comments/delete/'.$item->id,t('delete'));
                   }
                   
                   echo $control;
                   ?>
    </div>
</div>