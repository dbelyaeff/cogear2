$.fn.scrollTo = function(config){
    options = {
        duration: 500
    }
    $.extend(options,config);
    $target = $(this);
    if($target.hasClass('hl')){
        options.callback = function(){
            $(this).toggleClass('hltd',500);
        };
    }
    if($target.height() > $(window).height()){
        $top = $target.offset().top - ($(window).height()/2);
    } else {
        $top = $target.offset().top-(($(window).height()/2)-($target.height()/2));
    }
    $('html,body').animate({
        scrollTop:$top
    }, options.duration,$.proxy(options.callback,$target));
}
$(document).on('click.scrollTo','a.scrollTo',function(event){
    $href = $(this).attr('href');
    $target = $href.substring($href.search('#'));
    if($($target).length > 0){
        event.preventDefault();
    }
    $($target).scrollTo({
        highlight: $(this).hasClass('hltr')
    });
})
