<?php
//Database configuration file
	$host ="127.0.0.1";
	$dbusername = "ims";
	$dbpassword = "innovation";
	$dbname = "confluence4_bangalore";
	$title = "Ericsson";
	$jurysignums = array('EVYZACA','EFGJKMP','EBCEFFG','ESAPATI','EBOSKAS', 'ERAMGOP', 'EMOHVIJ');
		
	function db_connect()
	{
		global $host,$dbusername,$dbpassword,$dbname;
		if(!($link_id=mysql_pconnect($host,$dbusername,$dbpassword)))
		{
			echo("error connecting to db server!");
			exit();
		}
		// Select the Database
		if(!mysql_select_db($dbname,$link_id))
		{
			echo("error in selecting the database");
			echo(sprintf("Error : %d %s",mysql_errno($link_id),mysql_error($link_id)));
		}
		return $link_id;
	}
?>
