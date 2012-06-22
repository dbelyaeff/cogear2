$(document).ready(function(){
    setInterval(postAutosave, 10000)
})
function postAutosave(){
    $form = $('#form-post');
    $form.ajaxSubmit({
        url: $form.attr('action') + '?autosave=true',
        clearForm: false,
        type: 'post',
        dataType: 'json',
        resetForm: false,
        globalLoader: false
    });
}
