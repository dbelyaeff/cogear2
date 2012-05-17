$(document).on('mouseenter','i.icon-eye-open',function(){
    $(this).removeClass('icon-eye-open').addClass('icon-eye-close');
})
$(document).on('mouseleave','i.icon-eye-close',function(){
    $(this).removeClass('icon-eye-close').addClass('icon-eye-open');
})