var Converter = function(){
    this.init();
}
Converter.prototype = {
    steps: {},
    current: {},
    stopFlag: 0,
    init: function(){
        this.steps = $('.converter-step');
        this.setCurrent(0);
        this.bind();
        this.animateProgress();
        $('#converter-finish').hide();
    },
    setCurrent: function(index){
        $this = this;
        this.steps.each(function(i){
            if(i > index){
                $this.deactivate(i);
            } else if ( i == index){
                $this.activate(i);
            }
        })
    },
    getStep: function(index){
        return $(this.steps[index]);
    },
    showButton: function(name){
        this.current.find('.btn').each(function(){
            $action = $(this).attr('data-action');
            if($action != name){
                $(this).css('visibility','hidden');
            }
            else {
                $(this).css('visibility','visible');
            }
            if(name == 'progress' || name == 'success'){
                if($action == 'reset'){
                    $(this).css('visibility','visible');
                }
            }
        })
    },
    start: function() {
        this.stopFlag = 0;
        cogear.ajax.loader.show();
        this.request();
    },
    stop: function() {
        cogear.ajax.loader.hide();
        this.stopFlag = 1;
    },
    activate: function(index){
        $step = this.getStep(index);
        this.current = $step;
        $step.show();
        this.showButton('start');
    },
    deactivate: function(index){
        $step = this.getStep(index);
        $step.hide();
    },
    action: function(action,index){
        if(!index){
            index = parseInt(this.current.attr('data-key'));
        }
        $this = this;
        switch(action){
            case 'start':
                this.showButton('progress');
                cogear.ajax.loader.type('black-spinner').after(this.current.find('[data-action=progress]')).show();
                $this.start();
                break;
            case 'success':
                $this.stop();
                this.showButton('success');
                if(index < $this.steps.length - 1){
                    next = 1 + index;
                    this.setCurrent(next);
                    $this.action('start',next);
                }
                else {
                    $('#converter-finish').slideDown();
                }
                break;
            case 'reset':
                $target = this.getStep(index).attr('data-id');
                $.getJSON(l('/admin/converter/adapter/reset/?step=') + $target);
                $this.setCurrent(index);
                $this.current.find('.alert').html('');
                $this.stop();
                break;
        }
    },
    request: function(){
        $this = this;
        $current = this.current;
        if($this.stopFlag) return;
        $.getJSON($current.attr('data-source'),function(data){
            $info = $current.find('.alert').first();
            switch(data.status){
                case 'error':
                    $info.removeClass('alert-info').addClass('alert-error');
                    $info.html($info.html() + "<br/>" + data.text);
                    $info.scrollTop($info.prop('scrollHeight'));
                    $this.action('start');
                    break;
                case 'update':
                    if($info.hasClass('alert-error')){
                        $info.addClass('alert-info').removeClass('alert-error');
                    }
                    $info.html($info.html() + "<br/>" + data.text);
                    $info.scrollTop($info.prop('scrollHeight'));
                    if(!$this.stopFlag){
                        $this.request();
                    }
                    break;
                case 'finish':
                    $info.removeClass('alert-info').addClass('alert-success').html($info.html()+'<br/>'+data.text);
                    $this.action('success');
                    $this.stop();
                    break;
            }
        }).error(function(){
            $this.request();
        });
    },
    bind: function(){
        $this = this;
        $(document).on('click','.converter-step .btn',function(){
            $step = $(this).parent('.converter-step').first();
            $this.action($(this).attr('data-action'),$step.attr('data-key'));
        })
    },
    animateProgress: function(){
        $this = this;
        this.steps.find('[data-action=progress]').each(function(){
            $(this).hover(function(){
                $(this).find('.icon').removeClass('icon-refresh').addClass('icon-pause');
            }, function(){
                $(this).find('.icon').removeClass('icon-pause').addClass('icon-refresh');
            });
            $(this).on('click',function(){
                if($this.stop){
                    $(this).find('.icon').removeClass('icon-play').addClass('icon-refresh');
                    $this.start();
                } else {
                    $(this).find('.icon').removeClass('icon-pause').removeClass('icon-refresh').addClass('icon-play');
                    $this.stop();
                }
            });
        })
    }
}
$('document').ready(function(){
    cogear.converter = new Converter();
})