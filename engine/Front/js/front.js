$(document).on('click','.post .post-promote',function(event){
    event.preventDefault();
    $link = $(this);
    $post = $link.parents('.post').first();
    $promoted = $post.hasClass('promoted');
    $.getJSON($link.attr('href'),function(data){
        if(data.action == 'promote'){
            $link.addClass('promoted');
        }
        else {
            $link.removeClass('promoted');
        }
    });
});