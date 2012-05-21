(function($){
    $.fn.scrollTo = function(config){
        options = {
            duration: 500,
            callback: function(){
                if(config.highlight){
                    $(this).toggleClass('hl',500);
                }
            }
        }
        $.extend(options,config);
        $target = $(this);
        $('html,body').animate({
            scrollTop:($target.offset().top-(($(window).height()/2)-($target.height()/2)))
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
})(jQuery);