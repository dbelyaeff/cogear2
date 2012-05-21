var Comments = function(){
    this.init();
}
var Comments_Updater = function($type,$id){
    this.init($type,$id);
}
Comments_Updater.prototype = {
    el: $('<div id="comments-updater"><a id="comments-updater-director">&larr;</a><a id="comments-updater-handler"></a><a id="comments-updater-counter"></a></div>'),
    handler: null,
    director: null,
    form: null,
    counter: null,
    postCounter: null,
    target: {
        id: 0,
        type: 'post'
    },
    init: function($type,$id){
        this.target.type = $type;
        this.target.id = $id;
        $('body').append(this.el);
        this.director = $('#comments-updater-director',this.el);
        this.director.click(function(){
            
        })
        this.handler = $('#comments-updater-handler',this.el);
        this.handler.click($.proxy(function(){
            this.update();
        },this));
        $updater = this;
        this.counter = $('#comments-updater-counter',this.el);
        this.counter.action = function($id,$config){
            $options = {
                highlight: true,
                removeNew: true
            }
            $config && $.extend($options,$config);
            if(!$id){
                $id = 0;
                $('.comment.new').each(function(){
                    if(!$id || $(this).attr('data-id') < $id){
                        $id = $(this).attr('data-id');
                    }
                })
            }
            if($id && $('#comment-'+$id).length){
                $('#comment-'+$id).scrollTo({
                    callback: function(){
                        $options.highlight && $(this).toggleClass('hl',500);
                        $options.removeNew && $(this).removeClass('new',500);
                        $updater.recount();
                    }
                });
            }
        };
        this.counter.click($.proxy(function(){
            this.counter.action();
        },this));
        this.postCounter = $('#'+this.target.type+'-'+this.target.id+' .comments-new').first();
        this.keyboardListener();
        this.recount();
    },
    loading: function(action){
        switch(action){
            case 'start':
                this.handler.addClass('active');
                break;
            case 'stop':
                this.handler.removeClass('active');
                break;
            default:
                this.handler.toggleClass('active');
        }
    },
    recount: function(){
        count = $('.comment.new').length;
        this.counter.html(count ? count : '');
        this.postCounter.html(count ? '+' + count : '');
    },
    update: function(){
        $updater = this;
        $updater.loading();
        $.ajax({
            url: l('/comments/update/'+this.target.type+'/'+this.target.id+'/'),
            dataType: 'json',
            success: function(data){
                if(data.comments){
                    $count = 0;
                    $.each(data.comments,function(){
                        if(this.pid > 0){
                            $parent = $('#comment-'+this.pid);
                            $childs = $parent.getChildren('.comment');
                            if($childs.length > 0){
                                $($childs).last().after(this.body);
                            }
                            else {
                                $parent.after(this.body);
                            }
                        }
                        else{
                            $updater.form.holder.before(this.body);
                        }
                        $count++;
                    });
                    $updater.recount();
                    if(data.jp){
                        $updater.counter.action(data.jp,{
                            removeNew: false
                        });
                    }
                }
            },
            complete: function(){
                $updater.loading();
            },
            error: function(){
                $updater.loading();
            }
        });
    },
    keyboardListener: function(){
        $updater = this;
        $(document).keydown(function(event){
            // Alt + z
            if(event.keyCode == 90 && event.shiftKey && event.altKey){
                event.preventDefault();
                $updater.counter.click();
            }
            // Alt + x
            if(event.keyCode == 88 && event.shiftKey && event.altKey){
                event.preventDefault();
                $updater.handler.click();
            }
        });
    }
}
Comments.prototype = {
    form: null,
    init: function(){
        this.load();
        this.bind();
    },
    load: function(){
        $comments = this;
        $('.comments-handler').each(function(){
            $type = $(this).attr('data-type');
            $id = $(this).attr('data-id');
            $(this).load(l('/comments/load/'+$type+'/'+$id+'/ #comments'),function(){
                $comments.form = $('#form-comment',$comments.formHolder);
                $comments.form.holder = $('.comments-form-holder',$(this));
                $comments.form.repose = function(){
                    this.holder.append(this);
                    this.clearForm();
                    this.find('[name=pid]').removeAttr('value');
                }
                $comments.form.ajaxedForm(function(data){
                    if(data.success){
                        switch(data.action){
                            case 'preview':
                                $el = $comments.form.find('#form-comment-body');
                                $pbutton = $('#form-comment-preview-element');
                                $preview = $('<div class="comment-preview"/>');
                                $el.before($preview);
                                $preview.html(data.code);
                                $preview.hide();
                                $el.slideUp();
                                $preview.slideDown();
                                $pbutton.hide();
                                $(document).one('click',function(){
                                    $preview.remove();
                                    $pbutton.show();
                                    $el.slideDown();
                                })
                                break;
                            case 'publish':
                                $comments.updater.update();
                                $('[name=pid]',$comments.form).removeAttr('value');
                                if(data.counter){
                                    $comments.update('counter',data.post_id,data.counter);
                                }
                                $comments.form.repose();
                                break;
                        }
                    }
                    else {
                        validateForm('form-comment',data);
                    }
                });
                if(cogear.user.id){
                    $comments.updater = new Comments_Updater($type,$id);
                    $comments.updater.form = $comments.form;
                }
            });
        })
    },
    bind: function(){
        $comments = this;
        $(document).on('mouseover','.comment.new',function(){
            $(this).removeClass('new').delay(100,function(){
                $comments.updater.recount();
            });
        })
        $(document).on('click','#comments a[data-type=reply]',function(event){
            event.preventDefault();
            $source = $comments.form;
            var target = $(this).attr('data-target');
            $target = $('#'+target);
            var origin = $(this).attr('data-origin');
            $origin = $('#'+origin);
            if($target.find('#form-comment').length){
                $comments.form.repose();
            }
            else {
                $source.appendTo($target);
                $source.find('[name=pid]').attr('value',$target.attr('data-id'));
                $(document).off('click.reply').on('click.reply',function(event){
                    if($(event.target).attr('data-type')){
                        return;
                    }
                    if($(event.target).parents('#form-comment').length == 0){
                        $comments.form.repose();
                        $(document).off('click.reply');
                    }
                })
            }
        })

        $(document).on('click','.comment .comment-edit',function(event){
            event.preventDefault();
            $comment = $(this).parents('.comment').first();
            $body = $comment.find('.comment-body');
            $id = $(this).attr('data-id');
            if($comment.find('form').length == 0){
                $handler = $('<div></div>');
                $body.after($handler)
                $handler.load('/comments/edit/'+$id+' #form-comment',function(){
                    $form = $comment.find('form').first();
                    $form.find('textarea').first().css('height',$body.css('height')).elastic();
                    $body.hide();
                    $form.addClass('edit-inline');
                    $form.ajaxedForm(function(data){
                        if(data.success){
                            switch(data.action){
                                case 'preview':
                                    $el = $form.find('#form-comment-body');
                                    $pbutton = $form.find('[name=preview]').first();
                                    $preview = $('<div class="comment-body"/>');
                                    $el.before($preview);
                                    $preview.html(data.code);
                                    $preview.hide();
                                    $el.slideUp();
                                    $preview.slideDown();
                                    $pbutton.hide();
                                    $(document).one('click',function(){
                                        $preview.remove();
                                        $pbutton.show();
                                        $el.slideDown();
                                    })
                                    break;
                                case 'update':
                                    $body.html(data.code);
                                    $body.show();
                                    if(data.counter){
                                        $comments.update('counter',data.post_id,data.counter);
                                    }
                                    $form.parent().remove();
                                    break;
                            }
                        }
                        else {
                            validateForm('form-comment',data);
                        }
                    })
                });
                $form.attr('id','form-edit-'+$id);
            }
            else {
                $comment.find('form').first().parent().remove();
                $body.show();
            }
        })

        $(document).on('click','.comment .comment-hide',function(event){
            event.preventDefault();
            $link = $(this);
            $comment = $link.parents('.comment').first();
            bootbox.confirm(t($comment.hasClass('hidden')? 'Unhide comment and all of its childs?' : 'Hide comment and all of its childs?'), function(confirmed) {
                if(confirmed){
                    $.getJSON($link.attr('href'),function(data){
                        if(data.success){
                            if(data.action == 'hide'){
                                $comment.addClass('hidden');
                                $childs = $comment.getChildren('.comment');
                                if($childs.length){
                                    $($childs).addClass('hidden');
                                }
                                $link.find('i').first().removeClass('icon-eye-close').addClass('icon-eye-open');
                            }
                            else if(data.action == 'show'){
                                $comment.removeClass('hidden');
                                $childs = $comment.getChildren('.comment');
                                if($childs.length){
                                    $($childs).removeClass('hidden');
                                }
                                $link.find('i').first().removeClass('icon-eye-open').addClass('icon-eye-close');
                            }
                            if(data.counter){
                                this.update('counter',{
                                    post_id: data.post_id,
                                    counter: data.counter
                                });
                            }
                        }
                    });
                }
            });
        });
        $(document).on('click','.comment .comment-delete',function(event){
            event.preventDefault();
            $link = $(this);
            $comment = $link.parents('.comment').first();
            bootbox.confirm(t('Delete comment and all of its childs?'), function(confirmed) {
                if(confirmed){
                    $.getJSON($link.attr('href'),function(data){
                        if(data.success){
                            $comment.nextAll('.comment').each(function(){
                                if($(this).attr('data-level') > $comment.attr('data-level')){
                                    $(this).fadeOut('slow',function(){
                                        $(this).remove();
                                    });
                                }
                            });
                            $comment.slideUp().fadeOut('slow',function(){
                                $(this).remove();
                            });
                            if(data.counter){
                                this.update('counter',{
                                    post_id: data.post_id,
                                    counter: data.counter
                                });
                            }
                        }
                    });
                }
            });
        });
    },
    update: function(type,data){
        switch(type){
            case 'counter':
                $('#post-'+data.post_id+' .post-comments').text(data.counter);
                break;
        }
    }
}

$(document).ready(function(){
    cogear.comments = new Comments();
})

