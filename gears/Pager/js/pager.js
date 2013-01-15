$(document).ready(function(){
    $(window).on('keydown.pager',function(e){
        if(e.ctrlKey && e.keyCode == 37){
            var prev = $('.pagination .prev');
            if(prev.length){
                    document.location.href = prev.attr('href');
            }
        }
        if(e.ctrlKey && e.keyCode == 39){
            var next = $('.pagination .next');
            if(next.length){
                    document.location.href = next.attr('href');
            }
        }
    })
})