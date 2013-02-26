$(document).ready(function() {
    $('.dd-list').nestedSortable({
        handle: '.dd-handle',
        items: 'li',
        listType: 'ul',
        placeholder: 'dd-placeholder'
    }).attr('unselectable', 'on')
     .css('user-select', 'none')
     .on('selectstart', false);
    $('#dd-save').on('click', function() {
        $dd = $('.dd').first();
        $this = $(this);
        data = {
            items: $('.dd-list').nestedSortable('toHierarchy')
        }
        $.ajax({
            url: $dd.attr('data-saveuri'),
            data: data,
            dataType: 'json',
            type: 'POST',
            beforeSend: function() {
                cogear.ajax.loader.type('blue-dots').after($this).show();
            },
            complete: function() {
                cogear.ajax.loader.hide();
            }
        });
    });
})