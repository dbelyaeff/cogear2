$(document).ready(function(){
    $('.modal .modal-footer .modal-close').click(function(e){
        e.preventDefault();
        $(this).parent().parent().modal('hide');  
        return false;
    })
})