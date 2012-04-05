(function($){
    $.fn.expander = function(options){
        var settings = {}
        settings = $.extend(options,settings);
        $containers = $(this).find('.container');
        $(this).hasClass('collapsible') || $containers.hide();
        $(this).find('.handler').each(function(index){
            $(this).click(function(){
               $containers.eq(index).toggle();
               $(this).html($(this).html() == '-' ? '+' : '-');
            } );
        });
    }
    $(document).ready(function(){
        $('.collapsible').expander();
    });
})(jQuery)
