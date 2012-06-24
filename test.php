<?php

$data = 'Test, Новый тег!';


$result = preg_split('#[\s]*[,][\s]*#',$data);
echo '<pre>';
print_r($result);