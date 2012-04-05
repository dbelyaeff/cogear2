<?php
if(isset($loginza->photo)){
    echo HTML::img($loginza->photo);
}
?>

<?=$profile->genFullName()?>