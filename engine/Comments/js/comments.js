$(document).ready(function(){
    $('.comments-handler').each(function(){
        $type = $(this).attr('data-type');
        $id = $(this).attr('data-id');
        $(this).load('http://'+cogear.settings.site+'/comments/load/'+$type+'/'+$id+'/ #comments',function(){
            $form = $('#form-comment');
            $form.ajaxedForm(function(data){
                if(data.success){
                    switch(data.action){
                        case 'preview':
                            $el = $form.find('#form-comment-body');
                            $pbutton = $('#form-comment-preview-element');
                            $preview = $('<div class="comment-preview"/>');
                            $el.before($preview);
                            $preview.html(data.code);
                            $preview.hide();
                            $el.slideUp();
                            $preview.slideDown();
                            $pbutton.hide();
                            $(document).one('click',function(){
                                $preview.remove();
                                $pbutton.show();
                                $el.slideDown();
                            })
                            break;
                        case 'publish':
                            if(data.messages){
                                renderMessages(data.messages);
                            }
                            if(data.pid > 0){
                                $(data.code).insertAfter($('#comment-'+data.pid));
                            }
                            else {
                                console.log($form);
                                $(data.code).insertAfter($form);
                            }
                            $('#comment-form-placer').after($form);
                            if(data.counter){
                                commentsUpdatePostCounter($id,data.counter);
                            }
                            $form.clearForm();
                            break;
                    }
                }
                else {
                    validateForm('form-comment',data);
                }
            })
        });

    })


});

$('#comments a[data-type=reply]').live('click',function(event){
    event.preventDefault();
    $source = $('#form-comment');
    var target = $(this).attr('data-target');
    $target = $('#'+target);
    var origin = $(this).attr('data-origin');
    $origin = $('#'+origin);
    if($target.find('#form-comment').length){
        $('#comment-form-placer').after($source);
        $source.find('[name=pid]').removeAttr('value');
    }
    else {
        $source.appendTo($target);
        $source.find('[name=pid]').attr('value',$target.attr('data-id'));
        $(document).off('click.reply').on('click.reply',function(event){
            if($(event.target).attr('data-type')){
                return;
            }
            if($(event.target).parents('#form-comment').length == 0){
                $('#comment-form-placer').after($source);
                $source.find('[name=pid]').removeAttr('value');
            }
        })
    }
})

$(document).on('click','.comment .comment-edit',function(event){
    event.preventDefault();
    $comment = $(this).parents('.comment').first();
    $body = $comment.find('.comment-body');
    $id = $(this).attr('data-id');
    if($comment.find('form').length == 0){
        $handler = $('<div></div>');
        $body.after($handler)
        $handler.load('/comments/edit/'+$id+' #form-comment',function(){
            $form = $comment.find('form').first();
            $form.find('textarea').first().css('height',$body.css('height')).elastic();
            $body.hide();
            $form.addClass('edit-inline');
            $form.ajaxedForm(function(data){
                if(data.success){
                    switch(data.action){
                        case 'preview':
                            $el = $form.find('#form-comment-body');
                            $pbutton = $form.find('[name=preview]').first();
                            $preview = $('<div class="comment-body"/>');
                            $el.before($preview);
                            $preview.html(data.code);
                            $preview.hide();
                            $el.slideUp();
                            $preview.slideDown();
                            $pbutton.hide();
                            $(document).one('click',function(){
                                $preview.remove();
                                $pbutton.show();
                                $el.slideDown();
                            })
                            break;
                        case 'update':
                            if(data.messages){
                                renderMessages(data.messages);
                            }
                            $body.html(data.code);
                            $body.show();
                            if(data.counter){
                                commentsUpdatePostCounter(data.post_id,data.counter);
                            }
                            $form.parent().remove();
                            break;
                    }
                }
                else {
                    validateForm('form-comment',data);
                }
            })
        });
        $form.attr('id','form-edit-'+$id);
    }
    else {
        $comment.find('form').first().parent().remove();
        $body.show();
    }
})

$(document).on('click','.comment .comment-hide',function(event){
    event.preventDefault();
    $link = $(this);
    $comment = $link.parents('.comment').first();
    bootbox.confirm(t($comment.hasClass('hidden')? 'Unhide comment and all of its childs?' : 'Hide comment and all of its childs?'), function(confirmed) {
        if(confirmed){
            $.getJSON($link.attr('href'),function(data){
                if(data.success){
                    if(data.messages){
                        renderMessages(data.messages);
                    }
                    if(data.action == 'hide'){
                        $comment.addClass('hidden');
                        $comment.nextUntil('[data-level='+$comment.attr('data-level')+']').addClass('hidden');
                        $link.find('i').first().removeClass('icon-eye-close').addClass('icon-eye-open');
                    }
                    else if(data.action == 'show'){
                        $comment.removeClass('hidden');
                        $comment.nextUntil('[data-level='+$comment.attr('data-level')+']').removeClass('hidden');
                        $link.find('i').first().removeClass('icon-eye-open').addClass('icon-eye-close');
                    }
                    if(data.counter){
                        commentsUpdatePostCounter(data.post_id,data.counter);
                    }
                }
            });
        }
    });
});
$(document).on('click','.comment .comment-delete',function(event){
    event.preventDefault();
    $link = $(this);
    $comment = $link.parents('.comment').first();
    bootbox.confirm(t('Delete comment and all of its childs?'), function(confirmed) {
        if(confirmed){
            $.getJSON($link.attr('href'),function(data){
                if(data.success){
                    if(data.messages){
                        renderMessages(data.messages);
                    }
                    $comment.nextAll('.comment').each(function(){
                        if($(this).attr('data-level') > $comment.attr('data-level')){
                            $(this).fadeOut('slow',function(){
                                $(this).remove();
                            });
                        }
                    });
                    $comment.slideUp().fadeOut('slow',function(){
                        $(this).remove();
                    });
                    if(data.counter){
                        commentsUpdatePostCounter(data.post_id,data.counter);
                    }
                }
            });
        }
    });
});

function commentsUpdatePostCounter(post_id,counter){
    $('#post-'+post_id+' .post-comments').text(counter);
}