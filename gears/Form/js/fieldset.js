$(document).ready(function(){
    $('fieldset').each(function(){
        $this = $(this);
        if($this.hasClass('collapsed')){
            var $chevron = $('<i class="icon icon-chevron-right"/>')
        }
        else {
            var $chevron = $('<i class="icon icon-chevron-down"/>')
        }
        $legend = $this.find('> legend');
        if(!$legend.html()){
            $legend.hide();
            return;
        }
        $legend.append($chevron);
        var $wrapper = $("<div class='wrapper'/>");
        $legend.after($wrapper);
        $wrapper.nextAll().appendTo($wrapper);
        $legend.on('click',function(){
            if($this.hasClass('collapsed')){
                $wrapper.slideDown();
                $this.removeClass('collapsed');
                $chevron.removeClass('icon-chevron-right').addClass('icon-chevron-down');
            }
            else {
                $wrapper.slideUp(350,function(){
                    $this.addClass('collapsed');
                    $chevron.removeClass('icon-chevron-down').addClass('icon-chevron-right');
                });
            }
        })
        // Если в форме есть ошибки, fieldset должен быть раскрыт
        if($this.find('.error').length && $this.hasClass('collapsed')){
            $legend.click();
        }
    })
})