var Notify = function(){
}

Notify.prototype = {
    growl: function($body,$config){
        $options = {
            header: '',
            theme: null
        }
        $.extend($options,$config);
        if($config){
            $options.theme = 'alert-'+$config.type;
        }
        $.jGrowl($body,$options);
    },
    show: function(messages){
        $notify = this;
        $.each(messages,function(){
            $notify.growl(this.body,this);
        });
    }
}

$(document).ready(function(){
    cogear.notify = new Notify();
});

$(document).ajaxComplete(function(event,$xhr,$settings){
   if($settings.dataType == 'json'){
       $data = $.parseJSON($xhr.responseText);
       if($data.messages){
           cogear.notify.show($data.messages);
       }
   }
});