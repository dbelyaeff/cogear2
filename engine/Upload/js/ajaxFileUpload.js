(function($){
    window.ajaxFileUpload = {
        settings: {
            // replace, after, before
            mode: 'replace',
            target: null,
            handler: 'replace',
            action: '/upload',
            name: 'file',
            debug: 1
        },
        add: function(selector,options){
            options && $.extend(true,this.settings,options);
            $ajaxFileUpload = this;
            $this = $(selector);
            if($this.is('input[type=file]')){
                $name = $this.attr('name');
            }
            else {
                $name = $this.attr('name') || $this.attr('id');
            }
            // Operate handler
            switch(this.settings.handler){
                case 'replace':
                    $handler = $('<a/>').attr('href','#'+$name).text(t('Upload')).addClass('button ajaxed');
                    $this.replaceWith($handler);
                    $input = $('<input/>').attr({
                        'type': 'hidden',
                        'name': $name
                    });
                    $handler.before($input);
                    break;
                default:
                    $handler = $this;
            }
            // Make form
            if($handler.parents('form')){
                var action = $handler.parents('form').attr('action') || document.location.href;
            }
            else {
                var action = this.settings.action;
            }
            $form = $('<form/>').attr({
                'enctype': 'multipart/form-data',
                'action': action,
                'method': 'POST'
            });
            $file = $('<input/>').attr({
                'type':'file',
                'name':$name,
                'id':'file'
            });
            if($handler.parents('form').attr('id')){
                form_id = $handler.parents('form').attr('id').replace('form-','');
            }
            else {
                form_id = 'upload';
            }
            $hidden = $('<input/>').attr({
                'type':'hidden',
                'name':'form',
                'value':form_id
            });
            $form.append($file);
            $form.append($hidden);
            $form.hide();
            $form.appendTo($('body'));
            // Link form element with handler
            $handler.click(function(){
                $file.click(); 
            });
            // Add event to file
            $file.change(function(){
                $form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function(data){
                        $ajaxFileUpload.settings.debug && console.log(data);
                    },
                    beforeSend: function(){
                        $ajaxFileUpload.onStart();
                        $handler.loading();
                        $ajaxFileUpload.settings.debug && console.log('Start file uploading…');
                    },
                    success: function(response){
                        $handler.loading();
                        if(response.errors.length > 0){
                            $ajaxFileUpload.onFailure(response);
                            $errors = $handler.parent().find('.errors') || $('<div/>').addClass('errors').appendTo($handler.parent());
                            $errors.html(response.errors).show();
                            $ajaxFileUpload.settings.debug && console.log('File is failed to upload because of following errors:' + "\n" + response.errors) && console.log(response);
                        }
                        else {
                            $ajaxFileUpload.onSuccess(response);
                            $ajaxFileUpload.settings.debug && console.log('File is uploaded successfully!') && console.log(response);
                        }
                    },
                    error: function(response){
                        $ajaxFileUpload.onFailure();
                        $handler.loading();
                        console.log(response.responseText);
                        $ajaxFileUpload.settings.debug && console.log('File is failed to upload.') && console.log(response);
                    }
                });
            });
        },
        onStart: function(){
            
        },
        onSuccess: function(response){
            var code = this.process(response);
            var obj = this.settings.target ? $(this.settings.target) : $handler;
            switch(this.settings.mode){
                case 'replace':
                    obj.replaceWith(code);
                    break;
                case 'after':
                    obj.after(code);
                    break;
                case 'before':
                    obj.before(code);
                    break;
            }
            $input.val(response.value);
        },
        onFailure: function(){
            
        },
        process: function(response){
            var ext = response.file.split('.').pop();
            switch(ext){
                case 'jpg':
                case 'gif':
                case 'png':
                case 'ico':
                    var el = $('<img/>').attr({
                        'src': response.file,
                        'width': response.width,
                        'height': response.height,
                        'alt': ''
                    });
                    break;
                default:
                    var el = $('<a/>').attr({
                        'src': response.value,
                    }).text(response.file);
            }
            return el;
        }
    }

    $.fn.ajaxFileUpload = function(options){
        ajaxFileUpload.add($(this),options);
    }
})(jQuery);