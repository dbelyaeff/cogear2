$(document).ready(function(){
    autoselect = function(){
        $link = $('form input[name=link]');
        $pid = $('form select[name=pid]');
        $.ajax({
            url: l('/admin/pages/ajax/getLink/'+$pid.val()),
            dataType: 'json',
            beforeSend: function(){
              cogear.ajax.loader.type('blue-dots').after($pid).show();
              $link.prop('disabled',true);
            },
            complete: function(){
              cogear.ajax.loader.hide();
              $link.removeProp('disabled');
            },
            success: function(data){
                if(data.success){
                    $link.val(data.link + '/');
                    $link.focus();
                }
            }
        });
    };
    $('form').on('change', 'select[name=pid]',autoselect);
    $handler = $('<i class="icon icon-refresh"/>');
    $handler.css('cursor','pointer');
    $('#form-page-link label').append($handler);
    $handler.on('click',autoselect);
    if($('[name=pid]').val() != 0 && $('[name=link]').val() == ''){
        $('[name=pid]').trigger('change');
    }
});