(function($){
    $.fn.cgTabs = function(containers,options){
        var settings = {
            handler: 'a',
            trigger: 'click',
            current: 'active',
            index: 0
        }
        options && $.extend(settings,options);
        $this = $(this);
        $handlers = $this.find(settings.handler);
        $containers = $(containers);
        $handlers.each(function(a){
            if(document.location.href.search('#'+$(this).attr('id')) != -1){
                settings.index = a;
            }
        });
        $handlers.each(function(a){
            if(a == settings.index){
                $(this).addClass(settings.current);
                $($containers[a]).show();  
            }
            else {
                $(this).removeClass(settings.current);
                $($containers[a]).hide();  
            }
            $(this).bind(settings.trigger,function(){
                $(this).addClass(settings.current);
                $handlers.not($(this)).removeClass(settings.current);
                $(this).prevAll().removeClass(settings.current);
                $containers.each(function(b){
                    if(a == b){
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
                });
            })
        })
    
    } 
})(jQuery)