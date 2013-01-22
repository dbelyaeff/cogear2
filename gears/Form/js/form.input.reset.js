$.fn.resetButton = function(){
    $(this).each(function(){
        var $el = $(this);
        $el.wrap($('<div class="element-wrapper"/>'));
        var $wrapper = $el.parent();
        $wrapper.attr('id',$el.attr('id')+'-wrapper');
        var $reset = $('<i class="icon icon-remove"></i>');
        $wrapper.append($reset);
        $wrapper.css({
            position:'relative',
            display: $wrapper.parent('[class$="pend"]').length ? 'inline-block' : 'block'
        } );
        $reset.css({
            position: 'absolute',
            right: $wrapper.parent('[class$="pend"]').length ? '5px' : $wrapper.width() - $el.width() - 7  + 'px',
            top: '8px',
            cursor: 'pointer',
            opacity: '0.3'
        })
        $reset.hover(function(){
            $(this).css('opacity',1);
        }, function(){
            $(this).css('opacity',0.3);
        });
        $reset.bind('click',function(){
            $el.val('');
            $el.trigger('change')
            $(this).hide();
        })
        $reset.hide();
        $(this).on('keydown keyup change',function(event){
            if(event.type == 'keydown' && event.keyCode == 46 && event.ctrlKey == true){
                $reset.trigger('click')
            }
            if($(this).val()){
                $reset.show();
            }
            else {
                $reset.hide();
            }
        });
        $el.trigger('change')
    });
}
$(document).ready(function(){
    $('input[type=text], input[type=password], input[type=search]').resetButton();
})