$(document).ready(function(){
    $('.dd').nestable({
        listNodeName: 'ul',
        expandBtnHTML: '',
        collapseBtnHTML: ''
        }).attr('unselectable', 'on')
    .on('selectstart', false);
    $('#dd-save').on('click', function() {
        $dd = $('.dd').first();
        $this = $(this);
        data = {
            items: $('.dd').nestable('serialize')
        }
        $.ajax({
            url: $dd.attr('data-saveuri'),
            data: data,
            dataType: 'json',
            type: 'POST',
            beforeSend: function(){
                cogear.ajax.loader.type('blue-dots').after($this).show();
            },
            complete: function(){
                cogear.ajax.loader.hide();
            }
        });
    });
})