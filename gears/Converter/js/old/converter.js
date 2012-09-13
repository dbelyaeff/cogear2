var Converter = function(){
    this.init();
}
Converter.prototype = {
    steps: {},
    currentStep: 0,
    current: 0,
    stop: 0,
    init: function(){
        $conv = this;
        $('#converter-finish').hide();
        this.steps = $('.converter-step');
        this.steps.find('[data-action=inprogress]').each(function(){
            $(this).hover(function(){
                $(this).find('.icon').removeClass('icon-refresh').addClass('icon-pause');
            }, function(){
                $(this).find('.icon').removeClass('icon-pause').addClass('icon-refresh');
            });
            $(this).on('click',function(){
                if($conv.stop){
                    $(this).find('.icon').removeClass('icon-play').addClass('icon-refresh');
                    $conv.stop = 0;
                } else {
                    $(this).find('.icon').removeClass('icon-pause').removeClass('icon-refresh').addClass('icon-play');
                    $conv.stop = 1;
                }
            });
        })
        this.steps.find('.btn').on('click',function(){
            $action = $(this).attr('data-action');
            $target = $(this).attr('data-target');
            switch($action){
                case 'start':
                    $action = 'inprogress';
                    $conv.action($action),;
                    break;
                //                case 'inprogress':
                //                    $action = 'success';
                //                    break;
                //                case 'success':
                //                    $action = 'next';
                case 'reset':
                    $.getJSON(l('/admin/converter/adapter/reset/?step=') + $target);
                    $conv.setCurrent($(this).parent('.converter-step').prevAll('.converter-step').length);
                    $conv.stop = 1;
                    break;
            }
        });
        this.setCurrent(0);
    },
    setCurrent: function(index){
        $conv = this;
        this.steps.each(function(i){
            if(i != index){
                $(this).find('.btn').css('visibility','hidden');
                if(i < index){
                    $(this).find('[data-action=reset]').css('visibility','visible');
                    $(this).find('[data-action=success]').css('visibility','visible');
                }
            }
            else {
                $(this).show();
                $conv.current = $(this);
                $conv.action('start');
            }
            if(index <= i){
                $(this).find('.alert').hide();
            }
        });
        $conv.currentStep = index;
        $conv.current.find('.alert').html('');
        $conv.current.find('[data-action=start]').click();
    },
    action: function(action){
        $conv = this;
        $conv.current.find('.btn').css('visibility','hidden');
        $btn = this.current.find('[data-action='+action+']');
        $btn.css('visibility','visible');
        switch(action){
            case 'inprogress':
                this.current.find('.alert').slideDown();
                $a = 0;
                setInterval(function(){
                    if($conv.stop == 0){
                        $btn.find('i.icon-refresh').transition({
                            rotate: $a+'deg'
                        });
                        $a += 180;
                    }
                },1000);
                $conv.current.find('[data-action=reset]').css('visibility','visible');
                $conv.stop = 0;
                this.request();
                break;
            case 'success':
                $conv.current.find('[data-action=reset]').css('visibility','visible');
                if($conv.currentStep < $conv.steps.length){
                    this.setCurrent(this.currentStep+1);
                }
                else if(this.currentStep == this.steps.length){
                    $('#converter-finish').slideDown();
                }
                break;
        }
    },
    request: function(){
        $conv = this;
        $current = $(this.current);
        $.getJSON($current.attr('data-source'),function(data){
            $info = $current.find('.alert').first();
            switch(data.status){
                case 'error':
                    $info.removeClass('alert-info').addClass('alert-error');
                    $info.html($info.html() + "<br/>" + data.text);
                    $info.scrollTop($info.prop('scrollHeight'));
                    $conv.action('start');
                    break;
                case 'update':
                    if($info.hasClass('alert-error')){
                        $info.addClass('alert-info').removeClass('alert-error');
                    }
                    $info.html($info.html() + "<br/>" + data.text);
                    $info.scrollTop($info.prop('scrollHeight'));
                    if($conv.stop  == 0){
                        $conv.request();
                    }
                    break;
                case 'finish':
                    $info.removeClass('alert-info').addClass('alert-success').html($info.html()+'<br/>'+data.text);
                    $conv.action('success');
                    break;
            }
        })
    }
}
$('document').ready(function(){
    cogear.converter = new Converter();
})