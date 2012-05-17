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
    $('body').on('click','form .controls',function(){
        $group  = $(this).parent('.control-group');
        if($group.hasClass('error')){
            $group.removeClass('error');
            $group.find('.help-inline').remove();
        }
    });
})
