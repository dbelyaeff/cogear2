$(document).ready(function(){
   $('input[name^=delete], a.delete').click(function(event){
      if($(this).attr('rel') != 'yes'){
        $(this).confirm(t('Are you sure to delete?'));
        event.preventDefault();
      }
      else {
          $(this).loading();
      }
   }); 
})