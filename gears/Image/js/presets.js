$(document).ready(function(){
    $('a.preset').each(function(){
        $a = $(this);
        $a.fancybox({
            maxWidth	: 800,
            maxHeight	: 500,
            fitToView	: true,
            autoSize	: true,
            openEffect  : 'elastic',
            closeEffect : 'elastic',
        });

    });
})