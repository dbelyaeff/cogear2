$(document).ready(function(){
    $('form input[data-source].taxonomy').each(function(){
        $el = $(this);
        $el.autocomplete({
            source: $el.attr('data-source'),
            globalLoader: false,
            minLength: 2
        })
    });
})