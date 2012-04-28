(function($){
    $.fn.loading = function(options){
        settings = {
            'type': 'after'
        }
        $.extend(settings,options);
        if(!$('#ajax-indicator').length){
            $('<div/>').attr('id','ajax-indicator').addClass('ajax-indicator').prependTo($('body'));
        }
        if(this.next().attr('id') == 'ajax-indicator'){
            $('#ajax-indicator').hide().appendTo($('body'));
        }
        else {
            $(this).after($('#ajax-indicator').show());
        }
    }
    $(document).ready(function(){
        $('.ajaxed').live('click',function(){
            $(this).loading();
        })
    });
})(jQuery);