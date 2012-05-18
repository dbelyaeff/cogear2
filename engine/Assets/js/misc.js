/**
 * Smooth scrolling
 */
$(document).ready(function(){
    $(window).hashchange(function(event){
        event.preventDefault();
        smoothScroll();
    });
    $(window).hashchange();
})
function smoothScroll(){
    if($(location.hash).length > 0){
        $(location.hash).ScrollTo();
    }
}

function l(link){
    return 'http://'+cogear.settings.site+link;
}
/**
 * Icons
 */
$(document).on('mouseenter','i.icon-eye-open',function(){
    $(this).removeClass('icon-eye-open').addClass('icon-eye-close');
})
$(document).on('mouseleave','i.icon-eye-close',function(){
    $(this).removeClass('icon-eye-close').addClass('icon-eye-open');
})