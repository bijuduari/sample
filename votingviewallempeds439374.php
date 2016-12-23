<?php

error_reporting(E_ALL ^ E_NOTICE);

include ("connection.php");
$linkid = db_connect();

echo "<html><head><style>
table,th,td
{
border:1px solid black;
}
</style></head><body><table style='border: 0px;' width='100%'><tr><td width='80%' style='border: 0px;' align='center'><h3>CSI ADM Confluence 3.0 (Voting Portal) User Data</h3></td></tr><tr></table><br>View all user data:<br>
<table width='100%' border=1><tr bgcolor=\"#00FFFFF\"><td>voters_id</td><td>Signum_ID</td><td>Location</td><td>Manager_Signum</td><td>Department</td><td>Booth_ID</td><td>Informative</td><td>Innovativeness</td><td>Overall_Presentability</td><td>Updated_Time</td></tr>";
$SQL = "select * from voters;";
$RESULT = mysql_query($SQL);
if (mysql_num_rows($RESULT) > 0) {
	while ($OBJ = mysql_fetch_object($RESULT)) {
		echo "<td>".$OBJ->voters_id."</td>";
		echo "<td>".$OBJ->Signum_ID."</td>";
		echo "<td>".$OBJ->Location."</td>";
		echo "<td>".$OBJ->Manager_Signum."</td>";
		echo "<td>".$OBJ->Department."</td>";
		echo "<td>".$OBJ->Booth_ID."</td>";
		echo "<td>".$OBJ->Informative."</td>";
		echo "<td>".$OBJ->Innovativeness."</td>";
		echo "<td>".$OBJ->Overall_Presentability."</td>";
		echo "<td>".$OBJ->Updated_Time."</td></tr><tr>";
	}
}
echo "</table><br><br>";
echo "</body></html>";

?>
