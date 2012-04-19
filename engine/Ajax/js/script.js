$(document).ready(function(){
    $(window).hashchange(function(e){
        if(location.hash.charAt(1) == '/' || location.hash.charAt(1) == '?'){
            url = location.hash.substr(1);
            $.getScript(url);
            location.hash = '';
            location.href.replace('#','');
        }
        e.preventDefault();
    });
    $(window).hashchange();

})
