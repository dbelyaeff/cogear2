$(document).ready(function(){
	$('form div.errors.active').each(function(){
		var target = $(this).parent().addClass('clearfix error');
		$(this).slideDown("slow");        
    });
});
