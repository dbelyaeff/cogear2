function showImageUpload(){
    $.fancybox({
        href: '#form-image-upload',
        maxWidth	: 600,
        maxHeight	: 300,
        fitToView	: false,
        width		: 600,
        height		: 260,
        autoSize	: false,
        openEffect	: 'elastic',
        closeEffect	: 'elastic',
        afterShow: function(){
            $el = $('#form-image-upload-image-element');
            $el.fileupload({
                dataType: 'json',
                done: function (e, response) {
                    data = response.result;
                    if(data.messages){
                        renderMessages(data.messages);
                    }
                    if(data.success){
                        $.markItUp({
                            replaceWith:data.code
                        });
                        $.fancybox.close();
                    }
                }
            });
        }
    });

}