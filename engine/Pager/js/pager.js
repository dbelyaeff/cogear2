$(document).ready(function(){
    $('.pager.ajaxed').live('mouseover',function(){
        $pager = $(this);
        $(this).find('a').unbind('.nav').bind('click.nav',function(e){
            e.preventDefault();
            $('#'+$pager.attr('rel')).load($(this).attr('href')+' #'+$pager.attr('rel'));
        }) 
    });
    $(window).bind('keyup.pager',function(e){
        var ajaxed = $('.pager.ajaxed');
        if(ajaxed.length > 0){
            var rel = $(ajaxed).attr('rel');
        }
        if(e.ctrlKey && e.keyCode == 37){
            var prev = $('.pager .prev');
            if(prev.length){
                if(rel){
                    $('#'+rel).load(prev.attr('href')+' #'+rel);
                }
                else {
                    document.location = prev.attr('href');
                }
            }
        }
        if(e.ctrlKey && e.keyCode == 39){
            var next = $('.pager .next');
            if(next.length){
                if(rel){
                    $('#'+rel).load(next.attr('href')+' #'+rel);
                }
                else {
                    document.location = next.attr('href');
                }
            }
        }
    })
})