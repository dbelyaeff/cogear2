$('body').on('click','.modal .modal-close',function(e){
    e.preventDefault();
    $(this).parents('.modal').modal('hide');
})
$('.modal').live('shown',function () {
    var modal = $(this);
    modal.live('keydown',function(e){
        if(e.keyCode == 13 && e.ctrlKey){
            modal.find('.btn-primary').click();
        }
    });
    modal.find('.modal-body').load(modal.attr('data-source'),function(){
        var headers = modal.find('.page-header');
        if(headers.length > 0){
            header = headers[0];
            if(modal.find('.modal-header').length > 0){
               $(modal.find('.modal-header')[0]).html($(header).html());
            }
            else {
                $(header).removeClass('page-header').addClass('modal-header');
                $(modal.find('.close')[0]).after($(header));
            }
        }
        var actions = modal.find(' .modal-body .form-actions');
        if(actions.length && modal.find('.modal-footer').length == 0){
            $('<div class=\"modal-footer\"/>').appendTo(modal);
            actions.children().each(function(){
                var clone = $(this).clone();
                clone.appendTo(modal.find('.modal-footer')).bind('click',{
                    source:$(this)
                },function(e){
                    e.data.source.click();
                });
            });

        }
        if(actions.length) actions.hide();
    });
})
$(document).on('click','a[data-type="modal"]',function(e){
    e.preventDefault();
    var link = $(this).attr('href')+'?modal';
    if($(this).attr('data-source')) link += '='+$(this).attr('data-source');
    var obj = $(this);
    $.getScript(link,function(){
        if(obj.attr('data-width')){
            $('#modal-ajax').css({
                width: obj.attr('data-width')+'px',
                marginTop: '-'+obj.attr('data-width')/2+'px',
            });
        }
        if(obj.attr('data-height')){
            $('#modal-ajax').css({
                height: obj.attr('data-height')+'px',
                marginTop: '-'+obj.attr('data-height')/2+'px',
            });
        }
    });
    return false;
});
$(document).on('hidden','.modal.destroyable',function(){
    $(this).remove();
});
