$(document).on('click','.post .post-hide',function(event){
    event.preventDefault();
    $link = $(this);
    $post = $link.parents('.post').first();
    bootbox.confirm(t($post.hasClass('draft')? 'Publish post?' : 'Hide post to drafts?','Post'), function(confirmed) {
        if(confirmed){
            $.getJSON($link.attr('href'),function(data){
                if(data.success){
                    if(data.action == 'hide'){
                        $post.addClass('draft');
                        $link.find('i').first().removeClass('icon-eye-open').addClass('icon-eye-close');
                    }
                    else if(data.action == 'show'){
                        $post.removeClass('draft');
                        $link.find('i').first().removeClass('icon-eye-close').addClass('icon-eye-open');
                    }
                }
            });
        }
    });
});
$(document).on('click','.post .post-delete',function(event){
    event.preventDefault();
    $link = $(this);
    $post = $link.parents('.post').first();
    bootbox.confirm(t('Shure to delete this post?'), function(confirmed) {
        if(confirmed){
            $.getJSON($link.attr('href'),function(data){
                if(data.success){
                    $post.slideUp().fadeOut('slow',function(){
                        $(this).remove();
                        if(data.redirect){
                            window.location.href = data.redirect;
                        }
                    });
                }
            });
        }
    });
});

