$(document).ready(function() {
    $(".fancybox").fancybox({
        maxWidth	: 800,
        maxHeight	: 500,
        fitToView	: false,
        width		: 800,
        height		: 415,
        autoSize	: false,
        openEffect	: 'elastic',
        closeEffect	: 'elastic'
    });
    $('.post-body a > img').each(function(){
        $(this).parent().addClass('fancybox');
    })
    $('.chat-msg-text a > img').each(function(){
        $(this).parent().addClass('fancybox');
    })
});
cogear.fancybox = {};
cogear.fancybox.settings = {
    type: 'iframe',
    fitToView	: false,
    width		: '90%',
    height		: '90%',
    autoSize	: false,
    openEffect	: 'elastic',
    closeEffect	: 'elastic'
};
$(document).on('ajax.json',function(event,$data){
    if($data.action == 'fancybox'){
        $settings = $.extend({},cogear.fancybox.settings,$data.settings);
        console.log($settings);
        $.fancybox($settings);
    }
});