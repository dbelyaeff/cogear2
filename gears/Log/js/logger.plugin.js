function logger_ajax(el){
    $this = $(el);

}
$.fn.logger = function(url,data){
    $this = $(this);
    $.getJSON(url,data,function(data){
        if(data.action){
            switch(data.action){
                case 'reset':
                    $this.html('');
                    break;
            }
        }
        $this.removeClass('hidden').show();
        $this.html($this.html() + data.text + "<br/>" );
        $this.trigger('loggerReply',[data]);
        if(data.success){
            $this.logger(url,{});
        }
        else {
            cogear.ajax.loader.hide();
            $this.trigger('loggerStop',[data]);
        }
    });

}