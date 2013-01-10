$(document).ready(function(){
    $handler = $('<i class="icon icon-bookmark"></i>');
    $('#admin-menu').append($handler);
    $handler.css({
        position: 'absolute',
        top: 38,
        right: 50,
        cursor: 'pointer'
    })
    $handler.on('click',function(event){
        speed = 100;
        $top = $('#admin-menu').css('top');
        if($top == '0px'){
            $('#admin-menu').animate({
                'top':'-40px'
            },speed)
            $('#navbar-user').animate({
                'marginTop':'0'
            },3*speed);
            $.cookie('a',1);
        }
        else {
            $('#admin-menu').animate({
                'top':'0'
            },speed)
            $('#navbar-user').animate({
                'marginTop':'40px'
            },3*speed);
            $.cookie('a',0,{expires: 0});
        }
    })
    if(1 == $.cookie('a')){
        $('#admin-menu').css({
            top:'-40px'
        })
        $('#navbar-user').css({
            marginTop:'0px'
        });
        $.cookie('a',1);
    }
    $(document).on('keydown',function(e){
        if(e.keyCode == 17){
            $handler.trigger('click');
        }
    })
})