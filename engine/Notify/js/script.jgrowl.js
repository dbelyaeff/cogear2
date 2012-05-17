function renderMessages(messages){
    $.each(messages,function(){
       var options = {};
       if(this.type){
           options.theme = 'alert-'+this.type;
       }
       if(this.header){
           options.header = this.header;
       }
       $.jGrowl(this.body,options);
    });
}