$(document).ready(function(){
    $links = $('.widget-controls a');
    $links = $.merge($links,$('.region-controls a'));
    $links.on('click',function(event){
        event.preventDefault();
        $link = $(this);
        $.getJSON($link.attr('href')+'?dispatcher');
    });
})