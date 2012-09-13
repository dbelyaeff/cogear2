<a onclick="$(this).next().toggle();"><?php echo t('Session')?></a>
<pre id="session-debug" style="display:none;">
<?php echo var_export($_SESSION,TRUE)?>
</pre>