function showElFinder(){
    $('<div id="markitup-elfinder"/>').appendTo('body');
    $('#markitup-elfinder').elfinder({
        lang: 'ru',
        url : l('/files/connector'), 
        getFileCallback:function(file){
            $.markItUp({ replaceWith:getFileCodeFromLink(file)});
            $.fancybox.close();
        }
    }).elfinder('instance');
    $.fancybox({
        content: $('#markitup-elfinder'),
        maxWidth	: 800,
        maxHeight	: 500,
        fitToView	: false,
        width		: 800,
        height		: 415,
        autoSize	: false,
        openEffect	: 'elastic',
        closeEffect	: 'elastic'
    });
}

function getFileCodeFromLink(link){
    if(link.search(/\.(jpg|png|gif|ico)/i)){
        return '<img src="'+link+'" alt="">';
    }
    else {
        return '[file='+link+'"]';
    }
}