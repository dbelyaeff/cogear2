<?php
	
include "../config.php";


$_FILES['file']['type'] = strtolower($_FILES['file']['type']);

if ($_FILES['file']['type'] == 'image/png' 
|| $_FILES['file']['type'] == 'image/jpg' 
|| $_FILES['file']['type'] == 'image/gif' 
|| $_FILES['file']['type'] == 'image/jpeg'
|| $_FILES['file']['type'] == 'image/pjpeg')
{	
	$filename = md5(date('YmdHis')).'.jpg';	

	copy($_FILES['file']['tmp_name'], IMAGES_ROOT.$filename);
	echo '<img src="/tmp/images/'.$filename.'" />';
}
?>




