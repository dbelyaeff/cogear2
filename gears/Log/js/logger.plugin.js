function logger_ajax(el){
    $this = $(el);

}
$.fn.logger = function(url){
    $this = $(this);
    $.getJSON(url,function(data){
        if(data.success){
            $this.removeClass('hidden').show();
            $this.html($this.html + "\n" + data.text);
            $this.logger(url);
        }
        else {
            cogear.ajax.loader.hide();
        }
    });

}