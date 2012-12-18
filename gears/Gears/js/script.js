$(document).ready(function(){
   if($(location.hash).length > 0){
        $(location.hash).scrollTo();
        $(location.hash).addClass('info');
   }
});