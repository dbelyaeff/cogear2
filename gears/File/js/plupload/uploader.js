var Uploader = function(options){
    this.init(options);
}

Uploader.prototype = {
    options: {
        url: '',
        runtimes : 'gears,html5,html4,flash,browserplus',
        browse_button : null,
        container : null,
        drop_element: null,
        max_file_size: '2Gb',
        filters: [],
        autostart: true,
        flash_swf_url: l('/gears/file/js/plupload/plupload.flash.swf'),
        onAdd: function(){},
        onError: function(){},
        onComplete: function(){},
        uploadProgress: function(){}
    },
    uploader: {},
    init: function(options){
        if(options){
            this.options = $.extend(this.options,options)
        }
        cogear.upload = this;
        this.uploader = new plupload.Uploader(this.options);
        this.uploader.init();
        this.uploader.bind('FilesAdded', function(up, files) {
            cogear.upload.onAdd(files);
            up.refresh();
        });
        this.uploader.bind('UploadProgress', function(up, file) {
            cogear.upload.uploadProgress(file);
        });
        this.uploader.bind('Error', function(up, error) {
            cogear.upload.onError(error);
        });
        this.uploader.bind('FileUploaded', function(up, file, info) {
            info = info.response;
            if(info.charAt(0) == '{'){
                info = $.parseJSON(info);
            }
            cogear.upload.onComplete(info);
        });
        $('#'+this.options.drop_element).on('dragover',function(){
            $(this).addClass('dragover');
        }).on('dragleave',function(){
            $(this).removeClass('dragover');
        }).on('drop',function(){
            $(this).removeClass('dragover');
        })
    },
    onAdd: function(files){
        if(this.options.autostart){
            cogear.upload.uploader.start();
        }
        this.options.onAdd(files);
    },
    uploadProgress: function(file){
        this.options.uploadProgress(file);
    },
    onError: function(error){
        cogear.notify.growl(error.message,{
            header: error.file.name,
            type: 'error'
        })
        this.options.onError(error);
    },
    onComplete: function(data){
        $(document).trigger('ajax.json',data);
        this.options.onComplete(data);
    }
}