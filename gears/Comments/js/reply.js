$(document).ready(function(){
    $('.comment-reply').live('click',function(){
        $comment = $(this).parent().parent();
        reposeCommentForm('#comment-'+$(this).attr('rel'),$(this).attr('rel'));
    })
    $('.in-reply-to').live('click',function(){
        $target = $($(this).attr('href'));
        $target.effect('shake');
    });
    $('#comment-post-info').live({
        'mouseover': function(){
            reposeCommentForm();
        },
        'click': function(){
            reposeCommentForm();
        }
    })
})

function reposeCommentForm(target,reply){
    if(target){
        if($(target).find('form:visible').length){
            reposeCommentForm();
        }
        else {
            $('#form-comment').hide().appendTo(target).fadeIn();
        }
    }
    else if($('#comment-post-info').next() && $('#comment-post-info').next().attr('id' )!= 'form-comment'){
        $('#comment-post-info').hide().after($('#form-comment')).fadeIn();
        reply = '';
    }
    if(reply){
        $('#form-comment').find('[name=reply]').attr('value',reply);
    }
}