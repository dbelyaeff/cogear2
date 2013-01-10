<?php
$thumb = File::pathToUri(image_preset('image.thumb', UPLOADS . DS . $file->path));
?>
<a class="fancybox sh" href="<?php echo $file->getLink() ?>" class="file-<?php echo $file->info->getExtension() ?>">
    <img src="<?php echo $thumb ?>">
</a>