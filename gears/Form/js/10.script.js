jQuery.fn.ajaxedForm = function(success){
    return this.each(function(){
        $(this).prepend('<input type="hidden" name="ajaxed" value="'+$(this).attr('id')+'">');
        $(this).ajaxForm({
            dataType: 'json',
            type: 'post',
            success: success
        });
        $(this).removeClass('ajax').addClass('ajaxed');
    });
}

function validateForm($id,data){
    $.each(data.errors,function(element,errors){
        $controls = $('#'+$id+'-'+element);
        if($controls.hasClass('error')){

        }
        else {
            $controls.addClass('error');
        }
        if($controls.find('.help-inline').length > 0){
            $errors = $controls.find('.help-inline').first();
            $errors.text('');
        }
        else {
            $errors = $('<p class="help-inline"></p>');
            $controls.find('.controls').append($errors);
        }
        $.each(errors,function(){
            $errors.html(this + "<br/>" + $errors.html());
        })
    });
}
$(document).ready(function(){
    $('form').on('click','.error',function(){
        $(this).removeClass('error');
//        $group  = $(this);
//        $element = $('.form-element',$group);
//        $element.off('change.error').one('change.error',function(){
//            if($group.hasClass('error')){
//                $group.removeClass('error');
//                $group.find('.help-inline').remove();
//            }
//        });
    });
    $('.delete input').on('click',function(){
       if(!confirm(t('Вы действительно хотите это сделать?'))){
           return false;
       }
       return true;
    });
});
//$(document).on('keyup','form input[data-source].ajaxed,form textarea[data-source].ajaxed',function(event){
//    $el = $(this);
//    $source = $el.attr('data-source');
//    if($source){
//        $.ajax({
//            url: $source,
//            type: 'POST',
//            data: 'value='+$el.val(),
//            globalLoader: false,
//            dataType: 'json',
//            beforeSend: function(){
//                $el.loading();
//            },
//            complete: function(){
//                $el.loading();
//            }
//        });
//    }
//})
