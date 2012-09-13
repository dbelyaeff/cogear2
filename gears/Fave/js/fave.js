$(document).on('click','a.fave',function(event){
    event.preventDefault();
    $link = $(this);
    $.getJSON($link.attr('href'),function($data){
        console.log($data)
        switch($data.action){
            case 'fave':
                $link.addClass('faved');
                break;
            case 'unfave':
                $link.removeClass('faved');
                break;
        }
    });
});