var Chat = function($el){
    this.init($el);
}
Chat.prototype = {
    id: 0,
    el: {},
    init: function($el){
        this.el = $el;
        this.id = $el.attr('data-id');
        this.scroll();
        this.bind();
    },
    refresh: function(){
        $this = this;
        $(this.el).load(l('/chat/view/'+this.id+' #chat-window-'+this.id+''),{},function(){
            $this.scroll();
        });

    },
    scroll: function(){
        this.window = this.el.find('.chat-window')[0];
        $(this.window).scrollTop($(this.window).height());
    },
    bind: function(){

    }

};

$(document).ready(function(){
    $('.chat').each(function(){
        $chat = new Chat($(this));
        $form = $('#form-chat-msg');
        $form.ajaxForm({
            beforeSubmit: function(){
                $form.find('[type=submit]').after(cogear.ajax.inline.show());
            },
            success: function(){
                $chat.refresh()
                $form.find('[name=body]').val('');
            },
            complete: function(){
                cogear.ajax.inline.hide();
            }
        })
    })
    $('.chat-action').on('click',function(event){
        event.preventDefault();
        $link = $(this);
        bootbox.confirm(t('Are you sure?'), function(confirmed) {
            if(confirmed){
                $.getJSON($link.attr('href'),function(data){
                    if(data.success){
                        $link.parent().parent().slideUp();
                    }
                });
            }
        });
    })
})