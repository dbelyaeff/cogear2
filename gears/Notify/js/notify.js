var Notify = function(){
    this.init();
}

Notify.prototype = {
    init: function(){
        $notify = this;
        $(document).bind('ajax.json',function(event,$data){
            if($data.messages){
                $notify.show($data.messages);
            }
        })
        $.jGrowl.defaults.closer = false;
        $.jGrowl.defaults.check = 2000;
    },
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
