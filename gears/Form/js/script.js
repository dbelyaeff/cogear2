$(document).ready(function(){
    $('form .error').on('click',function(){
        $(this).removeClass('error');
        $(this).find('.help-inline').slideUp();
    });
    $('.delete').on('click',function(){
       if(!confirm(t('Вы действительно хотите это сделать?'))){
           return false;
       }
       return true;
    });
});
