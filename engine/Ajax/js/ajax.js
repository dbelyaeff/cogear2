var Ajax = function(){
    this.init();
}
var Ajax_Loader = function(){
    this.init();
}
Ajax_Loader.prototype = {
    el: '',
    options: {
        defaultClass: 'black-spinner'
    },
    className: 'ajax-loader',
    init: function(){
      this.el = $('<img src="'+l('/engine/Ajax/img/1x1.gif')+'" alt=""/>');
      this.type(this.options.defaultClass)
    },
    type: function(type){
        this.el.attr('class',this.className);
        this.el.addClass(type);
        return this;
    },
    after: function(selector){
        $(selector).after(this.el);
        return this;
    },
    before: function(selector){
        $(selector).before(this.el);
        return this;
    },
    show: function(){
        this.el.show();
        return this;
    },
    hide: function(){
        this.el.hide();
        return this;
    }
}
Ajax.prototype = {
    inline: {},
    loader: {},
    init: function(){
        this.bind();
        $ajax = this;
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
        this.loader = new Ajax_Loader();
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
    }
}

$(document).ready(function(){
    cogear.ajax = new Ajax();
})