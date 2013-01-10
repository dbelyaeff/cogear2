window.insertWysiwyg = function(text,selector){
    if(!selector){
        selector = 'textarea';
    }
    $(selector).each(function(){
        $this = $(this);
        if($this.redactor){
            $this.insertHtml(text);
        }
        else {
            $this.val($this.val() + text);
        }
    })
}