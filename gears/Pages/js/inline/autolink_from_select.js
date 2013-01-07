$(document).ready(function(){
    $('form').on('change', 'select[name=pid]',function(){
        $link = $('form input[name=link]');
        $pid = $(this);
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
    });
    if($('[name=pid]').val() != 0 && $('[name=link]').val() == ''){
        $('[name=pid]').trigger('change');
    }
});