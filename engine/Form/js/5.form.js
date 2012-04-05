$(document).ready(function(){
    $('label').click(function(){
     $(this).next().click().focus();   
    })
})