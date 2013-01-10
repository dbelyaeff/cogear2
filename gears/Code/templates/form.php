
<div id="editor"><?php echo htmlspecialchars($code) ?></div>
<script src="http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/eclipse");
    editor.getSession().setMode("ace/mode/php");
    editor.getSession().setUseWrapMode(true);
    $(document).ready(function(){
        $('form#form-code').on('submit',function(){
            $('#form-code-code-element').val(editor.getValue());
        })
        $("[name=type]").change(function(){
            editor.getSession().setMode("ace/mode/"+$(this).val());
        })
    });
</script>
<style>
    #editor {
        position: relative;
        height: 540px;
        width: 99%;
        border-top: 1px solid #CCC;
        border-bottom: 1px solid #CCC;
    }
    #form-code-type {
        text-align: right;
    }
</style>