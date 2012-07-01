(function($){
    //    $.fn.loading = function(options){
    //        settings = {
    //            'type': 'after'
    //        }
    //        $.extend(settings,options);
    //        if(!$('#ajax-indicator').length){
    //            $('<div/>').attr('id','ajax-indicator').addClass('ajax-indicator').prependTo($('body'));
    //        }
    //        if(this.next().attr('id') == 'ajax-indicator'){
    //            $('#ajax-indicator').hide().appendTo($('body'));
    //        }
    //        else {
    //            $(this).after($('#ajax-indicator').show());
    //        }
    //    }
    //    $(document).ready(function(){
    //        $('.ajaxed').live('click',function(){
    //            $(this).loading();
    //        })
    //    });
    $(document).bind('ajaxSend',function($event,$xhr,$options){
        console.log($options);
        $options.globalLoader && $('#ajax-loader').fadeIn('slow');
    }).bind('ajaxComplete',function($event,$xhr,$options){
        $options.globalLoader && $('#ajax-loader').fadeOut('slow');
    })
    $.ajaxSetup({
        globalLoader: cogear.settings.ajax.showLoader
    });
    cogear.loader = $('<div class="ajax-loader"></div>').hide();
    cogear.loader.hidden = true;
    $.fn.loading = function(action){
        $.each(this,function(){
            $el = $(this);
            $timeout = 1000;
            if(cogear.loader.hidden){
                console.log('show');
                cogear.loader.css({
                    'position': 'absolute',
                    'top': $el.position().top + 3,
                    'left': $el.position().left + $el.width() + 10
                })
                $el.after(cogear.loader);
                    console.log('show');
                cogear.loader.showtime = $.now();
                cogear.loader.show();
                cogear.loader.hidden = false;
                console.log(cogear.loader.hidden)
                setTimeout(function(){
                    console.log($.now());
                    $delta = $.now() - cogear.loader.showtime;
                    if(!cogear.loader.hidden && $delta > $timeout){
                        $el.loading();
                    }
                },$timeout)
            } else {
                $delta = $.now() - cogear.loader.showtime;
                if($delta > ($timeout - 100)){
                    console.log('hide');
                    cogear.loader.hide();
                    cogear.loader.hidden = true;
                }
            }
        });
    }
})(jQuery);