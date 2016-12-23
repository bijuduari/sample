<?php 


$str = "Some Junk content otest the truncate string so that it truncates meaningfully";

$new_str = substr($str, 0, strrpos(substr($str, 0, 50), ' '));


print $new_str . "\n";

?>
