$(document).ready(function(){
    $(window).bind('keyup.pager',function(e){
        if(e.ctrlKey && e.keyCode == 37){
            var prev = $('.pagination .prev');
            if(prev.length){
                    document.location = prev.attr('href');
            }
        }
        if(e.ctrlKey && e.keyCode == 39){
            var next = $('.pagination .next');
            if(next.length){
                    document.location = next.attr('href');
            }
        }
    })
})