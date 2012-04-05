<div id="cogear-dialog">
<script>
$(function() {
	$('#cogear-dialog').lightbox_me({
		centered: false,
		overlaySpeed: 100,
		closeClick:	false,
		modalCSS: {top: '140px'},
		overlayCSS:	{background: 'black', opacity: .2},
		destroyOnClose: true
	});
});
</script>

	<div class="cogear_modal">
		<div class="modal-header <?php echo $class?>"><?php echo $title?></div>
		<div class="modal-body"><?php echo $content?></div>
		<div class="modal-footer"><input type="button" class="close" value="<?=t("Close");?>"></div>
	</div>

</div>