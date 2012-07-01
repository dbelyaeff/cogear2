var Ajax = function(){
    this.init();
}
Ajax.prototype = {
    inline: {},
    init: function(){
        this.bind();
        $ajax = this;
        this.inline = $('<img src="'+l('/engine/Ajax/img/inline.loader.gif')+'" alt=""/>');
        $(document).ajaxSuccess(function(event,$xhr,$settings){
            if($settings.dataType == 'json' && $xhr.responseText[0] == '{'){
                $data = $.parseJSON($xhr.responseText);
                if(!$data){
                    $data = {};
                }
                $(document).trigger('ajax.json',[$data]);
                $ajax.dispatch($data);
            }
        })
    },
    bind: function(){
        $ajax = this;
        $(document).on('click.ajax','a.ajax',function(event){
            event.preventDefault();
            $link = $(this);
            $href = $(this).attr('href');
            $target = $('#'+$(this).attr('data-target'));
            if($(this).hasClass('ajax-load')){
                $target.load($href,function($response, $status, $xhr){
                    $(document).trigger('ajax.load',[$link,$target,$response,$status,$xhr]);
                });
            }
            else {
                $.getJSON($href,function($data){
                    $ajax.dispatch($data,$link);
                });
            }
        })
    },
    dispatch: function($data,$item){
        $ajax = this;
        if($data.action){
            if($data.action[0]){
                $.each($data.action,function(){
                    $ajax.action(this,$item);
                });
            }
            else {
                this.action($data.action,$item)
            }
        }
    },
    action: function(action,$item){
        if(!$item){
            $item = $(action.target);
        }
        if(!$item) return;
        switch(action.type){
            case 'class':
                action.className && $item.toggleClass(action.className);
                action.title && $item.attr('title',action.title);
                break;
            case 'replace':
                if(action.code){
                    $item.hide();
                    $item.after(action.code);
                    $item.remove();
                }
                break;
            case 'set':
                if(action.code){
                    $item.html(action.code);
                }
                break;
            case 'value':
                if(action.value){
                    $item.val(action.value);
                }
                break;
            case 'append':
                action.code && $item.append(action.code);
                break;
            case 'prepend':
                action.code && $item.prepend(action.code);
                break;

        }
    },
    loader: function(){
        if(cogear.settings.ajax.showLoader){
            return cogear.settings.ajax.showLoader = false;
        }
        else {
            return cogear.settings.ajax.showLoader = true;
        }
    }
}

$(document).ready(function(){
    cogear.ajax = new Ajax();
})