$(document).ready(function(){
    $('.navbar a[href="/user/login"]').click(function(){
        $('#modal-login').modal('show');
        return false;
    })
})