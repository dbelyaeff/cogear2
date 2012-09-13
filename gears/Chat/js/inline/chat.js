var Chat = function($el){
    this.init($el);
}
Chat.prototype = {
    id: 0,
    el: {},
    window: {},
    form: {},
    init: function($el){
        this.el = $el;
        this.window = this.el.find('.chat-window').first();
        this.id = $el.attr('data-id');
        this.form = $('#form-chat-msg');
        this.resize();
        this.scroll();
        this.bind();
        this.inviter();
        this.viewer();
        this.updateCounter();
        $chat = this;
        setInterval(function(){
            $chat.refresh();
        },5000);
        setInterval(function(){
            $chat.updateCounter();
        },5000);
    },
    refresh: function(){
        $this = this;
        $last = $('.chat-msg').last();

        $.getJSON('/chat/refresh/'+$this.id+'/'+$last.attr('data-id'),function(data){
            if(data.code){
                $this.window.append(data.code);
                $this.scroll();
            }
        })
    },
    resize: function(){
        $top = this.el.offset().top;
        $height = this.el.height();
        this.window.height($(window).height() - this.form.height() - 200);
    },
    scroll: function(){
        $(this.window).scrollTop($('.chat-msg').last().offset().top);
    },
    inviter: function(){
        $('#form-chat-invite').ajaxForm({
            dataType: 'json',
            success: function(data){
                if(data.code){
                    $('#chat-users-container').append($(data.code));
                }
                $('#form-chat-invite input[name=users]').val('');
            }
        })
    },
    viewer: function(){
        $chat = $(this);
        $(document).on('mouseover','.chat-msg.unviewed',function(){
            $(this).removeClass('unviewed');
            $.getJSON(l('/chat/viewer/'+$(this).attr('data-id')));
            $chat.updateCounter();
        });
    },
    updateCounter: function(){
        $('#navbar-msg-counter').load(l('/chat/counter/'));
    },
    bind: function(){
        $chat = $(this);
        $(document).resize(function(){
            $chat.resize();
        });
        $(document).keydown(function(e){
            console.log(e)
            if(e.keyCode == 13 && e.ctrlKey == true){
                $chat.form.find('[type=submit]').click();
            }
        })
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
    $(document).on('click','.chat-action',function(event){
        event.preventDefault();
        $link = $(this);
        bootbox.confirm(t('Are you sure?'), function(confirmed) {
            if(confirmed){
                if($link.hasClass('noajax')){
                    document.location = $link.attr('href');
                }
                else {
                    $.getJSON($link.attr('href'),function(data){
                        if(data.success){
                            if(data.code){
                                $link.parent().parent().replaceWith($(code));
                            }
                            else {
                                $link.parent().parent().slideUp();
                            }
                        }
                    });
                }
            }
        });
    })
})