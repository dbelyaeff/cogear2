$(document).ready(function(){
    $('.modal .modal-footer .modal-close').click(function(e){
        e.preventDefault();
        $(this).parent().parent().modal('hide');  
        return false;
    })
    $('.navbar a[href="/user/login"]').click(function(){
        $('#modal-login').modal('show');
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
})

