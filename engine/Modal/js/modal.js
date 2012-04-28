$(document).ready(function(){
    $('.modal .modal-close').live('click',function(e){
        e.preventDefault();
        $(this).parents('.modal').modal('hide');
        return false;
    })
    $('.modal[data-source]').live('shown', function () {
        var modal = $(this);
        modal.live('keydown',function(e){
            if(e.keyCode == 13 && e.ctrlKey){
                modal.find('.btn-primary').click();
            }
        });
        modal.find('.modal-body').load(modal.attr('data-source'),function(){
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
    $('a[data-type="modal"]').live('click',function(e){
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
})

