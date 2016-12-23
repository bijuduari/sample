<?php
	error_reporting(E_ALL ^ E_NOTICE);
	session_start();

$dirpath="/home/mtmuser/empmgmt/employeeinfo/website/EricssonValues";
//DEBUG-CHANGE
	$filename=$_REQUEST['file'];
if(file_exists("$dirpath/$filename")){
	$fp = fopen("$dirpath/$filename", "r");
	$data = "";
	while(!feof($fp))
 	{
		$data .=fgets($fp);
 	}
	fclose($fp);
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');
	print "$data";
}
	exit;
?>
