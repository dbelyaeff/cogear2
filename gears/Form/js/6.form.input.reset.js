$.fn.resetButton = function(){
    $el = $(this);
    $el.wrap($('<div class="element-wrapper"/>'));
    $wrapper = $el.parent();
    $reset = $('<i class="icon icon-remove"></i>');
    $wrapper.css('position','relative');
    $reset.css({
        position: 'absolute',
        right: '10px',
        top: '8px',
        cursor: 'pointer',
        opacity: '0.3'
    })
    $reset.hover(function(){
        $(this).css('opacity',1);
    }, function(){
        $(this).css('opacity',0.3);
    });
    $reset.on('click',function(){
        $el.val('');
        $(this).hide();
    })
    $reset.hide();
    $wrapper.append($reset);
    $el.on('keydown keyup change',function(){
        if($(this).val()){
            $reset.show();
        }
        else {
            $reset.hide();
        }
    });
    $el.trigger('change')
}
$(document).ready(function(){
    $('input[type=text], input[type=password], input[type=search]').resetButton();
})