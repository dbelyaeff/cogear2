$(document).ready(function(){
    $('fieldset').each(function(){
        $this = $(this);
        if($this.hasClass('collapsed')){
            $chevron = $('<i class="icon icon-chevron-right"/>')
        }
        else {
            $chevron = $('<i class="icon icon-chevron-down"/>')
        }
        $legend = $this.find('> legend');
        $legend.append($chevron);
        $wrapper = $("<div class='wrapper'/>");
        $legend.after($wrapper);
        $wrapper.nextAll().appendTo($wrapper);
        $legend.on('click',function(){
            if($this.hasClass('collapsed')){
                $wrapper.slideDown();
                $this.removeClass('collapsed');
                $chevron.removeClass('icon-chevron-right').addClass('icon-chevron-down');
            }
            else {
                $wrapper.slideUp();
                $this.addClass('collapsed');
                $chevron.removeClass('icon-chevron-down').addClass('icon-chevron-right');
            }
        })
    })
})