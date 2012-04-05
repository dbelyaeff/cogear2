$(document).ready(function(){
    $(window).hashchange(function(e){
        if(location.hash.charAt(1) == '/' || location.hash.charAt(1) == '?'){
            url = location.hash.substr(1);
            $.getJSON(url,function(data){
                if(data.items){
                    $.each(data.items,function(key){
                        switch(this.action){
                            case 'replace':
                                $('#'+this.id).replaceWith(this.code);
                                break;
                            case 'delete':
                                $('#'+this.id).remove();
                                break;
                        }
                    });
                }
                if(data.message){
                    if(data.message.title){
                        var t = data.message.title;
                    }
                    if(data.message.class){
                        var c = data.message.class;  
                    } 
                    var b = data.message.body;
                    message(b,t,c);
                    console.log(data.message)
                }
                if(data.action){
                    switch(data.action){
                        case 'reload':
                            window.location.reload();
                    }
                }
            })
            location.hash = '';
            document.location.href.replace('#','');
            $('#ajax-indicator').hide();
            return false;
        }
        e.preventDefault();
    });
    $(window).hashchange();
})
