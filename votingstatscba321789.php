<?php

error_reporting(E_ALL ^ E_NOTICE);

header("location:juryreview.html");

exit;
include ("connection.php");
$linkid = db_connect();

echo "<html><head><style>
table,th,td
{
border:1px solid black;
}
</style></head><body><table style='border: 0px;' width='100%'><tr><td width='80%' style='border: 0px;' align='center'><h3>CSI ADM Confluence 3.0 (Voting Portal) Statistics</h3></td></tr><tr></table><br>Total users casted the vote:<br>
<table width='100%' border=1><tr bgcolor=\"#00FFFFF\"><td>Count</td></tr>";
$SQL = "select count(distinct Signum_ID) as SIGCOUNT from voters";
$RESULT = mysql_query($SQL);
if (mysql_num_rows($RESULT) > 0) {
	while ($OBJ = mysql_fetch_object($RESULT)) {
		echo "<td>".$OBJ->SIGCOUNT."</td></tr>";
	}
}
echo "</table><br><br>";


echo "Total votes for each booth:<br>
<table width='100%' border=1><tr bgcolor=\"#00FFFFF\"><td>Booth_ID</td><td>Zone</td><td>Booth_Number</td><td>Type</td><td>Title</td><td>SPOC</td><td>Votes</td></tr>";
//$SQL = "select b.Booth_ID, b.Zone, b.Booth_Number, b.Type, b.Title, b.SPOC, count(v.Booth_ID) as Votes from booth b left join voters v on b.booth_id=v.booth_id group by v.Booth_ID order by b.Booth_ID;";
$SQL = "select b.Booth_ID, b.Zone, b.Booth_Number, b.Type, b.Title, b.SPOC from booth b";
$RESULT = mysql_query($SQL);
if (mysql_num_rows($RESULT) > 0) {
	while ($OBJ = mysql_fetch_object($RESULT)) {
		echo "<td>".$OBJ->Booth_ID."</td>";
		echo "<td>".$OBJ->Zone."</td>";
		echo "<td>".$OBJ->Booth_Number."</td>";
		echo "<td>".$OBJ->Type."</td>";
		echo "<td>".$OBJ->Title."</td>";
		echo "<td>".$OBJ->SPOC."</td>";
		$SQL = "select count(v.Booth_ID) as Votes from voters v where v.Booth_ID='".$OBJ->Booth_ID."'";
		$row1 = mysql_query($SQL);
		$rows= mysql_fetch_array($row1,MYSQLI_ASSOC);
		echo "<td>".$rows['Votes']."</td></tr><tr>";
	}
}
echo "</table><br><br>";
require 'conf.php';

$weightage1 = $informative_weightage;
$weightage2 = $innovative_weightage;
$weightage3 = $presentability_weightage;

echo "Rank for 'Live Lean' booth: Final Ratting=(Avg of Informative*$weightage1 + Avg of Innovativeness*$weightage2 + Avg of Overall_Presentability*$weightage3)<br>
<table width='100%' border=1><tr bgcolor=\"#00FFFFF\"><td>Booth_ID</td><td>Votes</td><td>Avg_Informative</td><td>Avg_Innovativeness</td><td>Avg_Overall_Presentability</td><td>Final Ratting</td></tr>";
#$SQL = "select Booth_ID, Avg_Informative, Avg_Innovativeness, Avg_Overall_Presentability, (Avg_Informative+Avg_Innovativeness+Avg_Overall_Presentability)/3 as Avg_Ranking, Vote_Count from (select Booth_ID, ROUND(avg(Informative),2) as Avg_Informative, ROUND(avg(Innovativeness),2) as Avg_Innovativeness,ROUND(avg(Overall_Presentability),2) as Avg_Overall_Presentability,sum(1) as Vote_Count from voters group by booth_id) q having Vote_Count > 4 order by Avg_Ranking desc;";
$SQL = "select Booth_ID, Avg_Informative, Avg_Innovativeness, Avg_Overall_Presentability, (Avg_Informative*$weightage1 + Avg_Innovativeness*$weightage2+Avg_Overall_Presentability*$weightage3) as Avg_Ranking, Vote_Count from (select t1.Zone, t2.Booth_ID, ROUND(avg(t2.Informative),2) as Avg_Informative, ROUND(avg(t2.Innovativeness),2) as Avg_Innovativeness,ROUND(avg(t2.Overall_Presentability),2) as Avg_Overall_Presentability, count(1) as Vote_Count from voters t2 join booth t1 on t1.Booth_ID=t2.Booth_ID where t1.Zone='Live Lean' group by t2.booth_id ) q having Vote_Count > 4 order by Avg_Ranking desc;";
$RESULT = mysql_query($SQL);
if (mysql_num_rows($RESULT) > 0) {
	while ($OBJ = mysql_fetch_object($RESULT)) {
		echo "<td>".$OBJ->Booth_ID."</td>";
		echo "<td>".$OBJ->Vote_Count."</td>";
		echo "<td>".$OBJ->Avg_Informative."</td>";
		echo "<td>".$OBJ->Avg_Innovativeness."</td>";
		echo "<td>".$OBJ->Avg_Overall_Presentability."</td>";
		echo "<td>".$OBJ->Avg_Ranking."</td></tr><tr>";
	}
}
echo "</table><br><br>";

echo "Rank for 'Think Big' booth: Final Ratting=(Avg of Informative*$weightage1 + Avg of Innovativeness*$weightage2 + Avg of Overall_Presentability*$weightage3)<br>
<table width='100%' border=1><tr bgcolor=\"#00FFFFF\"><td>Booth_ID</td><td>Votes</td><td>Avg_Informative</td><td>Avg_Innovativeness</td><td>Avg_Overall_Presentability</td><td>Final Ratting</td></tr>";
#$SQL = "select Booth_ID, Avg_Informative, Avg_Innovativeness, Avg_Overall_Presentability, (Avg_Informative+Avg_Innovativeness+Avg_Overall_Presentability)/3 as Avg_Ranking, Vote_Count from (select Booth_ID, ROUND(avg(Informative),2) as Avg_Informative, ROUND(avg(Innovativeness),2) as Avg_Innovativeness,ROUND(avg(Overall_Presentability),2) as Avg_Overall_Presentability,sum(1) as Vote_Count from voters group by booth_id) q having Vote_Count > 4 order by Avg_Ranking desc;";
$SQL = "select Booth_ID, Avg_Informative, Avg_Innovativeness, Avg_Overall_Presentability, (Avg_Informative*$weightage1 + Avg_Innovativeness*$weightage2+Avg_Overall_Presentability*$weightage3) as Avg_Ranking, Vote_Count from (select t1.Zone, t2.Booth_ID, ROUND(avg(t2.Informative),2) as Avg_Informative, ROUND(avg(t2.Innovativeness),2) as Avg_Innovativeness,ROUND(avg(t2.Overall_Presentability),2) as Avg_Overall_Presentability, count(1) as Vote_Count from voters t2 join booth t1 on t1.Booth_ID=t2.Booth_ID where t1.Zone='Think Big' group by t2.booth_id ) q having Vote_Count > 4 order by Avg_Ranking desc;";
$RESULT = mysql_query($SQL);
if (mysql_num_rows($RESULT) > 0) {
	while ($OBJ = mysql_fetch_object($RESULT)) {
		echo "<td>".$OBJ->Booth_ID."</td>";
		echo "<td>".$OBJ->Vote_Count."</td>";
		echo "<td>".$OBJ->Avg_Informative."</td>";
		echo "<td>".$OBJ->Avg_Innovativeness."</td>";
		echo "<td>".$OBJ->Avg_Overall_Presentability."</td>";
		echo "<td>".$OBJ->Avg_Ranking."</td></tr><tr>";
	}
}
echo "</table><br><br>";

echo "Rank for 'Go Green' booth: Final Ratting=(Avg of Informative*$weightage1 + Avg of Innovativeness*$weightage2 + Avg of Overall_Presentability*$weightage3)<br>
<table width='100%' border=1><tr bgcolor=\"#00FFFFF\"><td>Booth_ID</td><td>Votes</td><td>Avg_Informative</td><td>Avg_Innovativeness</td><td>Avg_Overall_Presentability</td><td>Final Ratting</td></tr>";
#$SQL = "select Booth_ID, Avg_Informative, Avg_Innovativeness, Avg_Overall_Presentability, (Avg_Informative+Avg_Innovativeness+Avg_Overall_Presentability)/3 as Avg_Ranking, Vote_Count from (select Booth_ID, ROUND(avg(Informative),2) as Avg_Informative, ROUND(avg(Innovativeness),2) as Avg_Innovativeness,ROUND(avg(Overall_Presentability),2) as Avg_Overall_Presentability,sum(1) as Vote_Count from voters group by booth_id) q having Vote_Count > 4 order by Avg_Ranking desc;";
$SQL = "select Booth_ID, Avg_Informative, Avg_Innovativeness, Avg_Overall_Presentability, (Avg_Informative*$weightage1 + Avg_Innovativeness*$weightage2+Avg_Overall_Presentability*$weightage3) as Avg_Ranking, Vote_Count from (select t1.Zone, t2.Booth_ID, ROUND(avg(t2.Informative),2) as Avg_Informative, ROUND(avg(t2.Innovativeness),2) as Avg_Innovativeness,ROUND(avg(t2.Overall_Presentability),2) as Avg_Overall_Presentability, count(1) as Vote_Count from voters t2 join booth t1 on t1.Booth_ID=t2.Booth_ID where t1.Zone='Go Green' group by t2.booth_id ) q having Vote_Count > 4 order by Avg_Ranking desc;";
$RESULT = mysql_query($SQL);
if (mysql_num_rows($RESULT) > 0) {
	while ($OBJ = mysql_fetch_object($RESULT)) {
		echo "<td>".$OBJ->Booth_ID."</td>";
		echo "<td>".$OBJ->Vote_Count."</td>";
		echo "<td>".$OBJ->Avg_Informative."</td>";
		echo "<td>".$OBJ->Avg_Innovativeness."</td>";
		echo "<td>".$OBJ->Avg_Overall_Presentability."</td>";
		echo "<td>".$OBJ->Avg_Ranking."</td></tr><tr>";
	}
}
echo "</table><br><br>";

echo "</body></html>";

?>
