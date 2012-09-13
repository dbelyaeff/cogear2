$(document).ready(function(){
    $('form input[data-source].autocomplete').each(function(){
        $el = $(this);
        $el.autocomplete({
            serviceUrl:  $el.attr('data-source'),
             delimiter: /(,|;)\s*/, // regex or character
            globalLoader: false,
            minChars: 2
        })
    });
})