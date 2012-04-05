<?

// default jGrowl properties
if(!isset($class)) $class = "info";
if(!isset($sticky)) $sticky = "false";
if(isset($title)){
	$header = "header: '$title',";
}else{
	$header = "";
}

?>
<script>
	(function($){
	$.jGrowl("<?php echo $content?>", {
		<?php echo $header?>
		sticky: <?php echo $sticky ?>,
		theme: '<?php echo $class ?>',
		speed: 'fast'
	});
	})(jQuery);
</script>