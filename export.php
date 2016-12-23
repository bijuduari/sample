<?php
	error_reporting(E_ALL ^ E_NOTICE);
	session_start();

	$file = $_REQUEST['filename'];
	$displayfile = preg_replace('/\//','_',$file);
//DEBUG-CHANGEfdgfdgfd
	$filename="/data/csiadm/ericssonvalues/$file";

if(file_exists($filename)){
	$fp = fopen("$filename", "r");
	$data = "";
	while(!feof($fp))
 	{
		$data .=fgets($fp);
 	}
	fclose($fp);
	header("Content-Type: video/mp4");
	header("Content-Disposition: attachment;filename=$displayfile");
	header("Cache-Control: max-age=0");
	print "$data";
}
	exit;
?>
