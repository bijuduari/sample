<?php
include ("connection.php");
$linkid = db_connect();

$live_lean_sql = "select Booth_ID, Booth_Number, Zone, round((Avg_Informative+Avg_Innovativeness+Avg_Overall_Presentability)/3,2) as Avg_Ranking, Vote_Count from (select v.Booth_ID, b.Booth_Number, Zone, ROUND(avg(Informative) ,2) as Avg_Informative,  ROUND(avg(Innovativeness), 2) as Avg_Innovativeness, ROUND(avg(Overall_Presentability),2) as Avg_Overall_Presentability, sum(1) as Vote_Count from voters v join booth b on (v.Booth_ID = b.Booth_ID) where b.zone='Live Lean' group by b.booth_id) q  having Vote_Count > $rank_eligible_vote_count order by Avg_Ranking desc";
$result = mysql_query($live_lean_sql);

print $live_lean_sql;

$live_lean = array();
print_r($live_lean_array);

exit;

$row_count = 0;
$rank_sum = 0;
while ($row = mysql_fetch_object($result)) {
  $each_row = array('booth_id' => $row->Booth_Number,
                    'avg_rank' => $row->Avg_Ranking               
  );
  $row_count++;
  $rank_sum += $row->Avg_Ranking;
  array_push($live_lean, $each_row);
}
$avg_live_lean_rank = round( $rank_sum/$row_count, 2);

$think_big_sql = "select Booth_ID, Booth_Number, Zone, round((Avg_Informative+Avg_Innovativeness+Avg_Overall_Presentability)/3,2) as Avg_Ranking, Vote_Count from (select v.Booth_ID, b.Booth_Number, Zone, ROUND(avg(Informative) ,2) as Avg_Informative,  ROUND(avg(Innovativeness), 2) as Avg_Innovativeness, ROUND(avg(Overall_Presentability),2) as Avg_Overall_Presentability, sum(1) as Vote_Count from voters v join booth b on (v.Booth_ID = b.Booth_ID) where b.zone='Live Lean' group by b.booth_id) q  having Vote_Count > $rank_eligible_vote_count order by Avg_Ranking desc";
$result = mysql_query($think_big_sql);

$think_big = array();

$row_count = 0;
$rank_sum = 0;
while ($row = mysql_fetch_object($result)) {
  $each_row = array('booth_id' => $row->Booth_Number,
                    'avg_rank' => $row->Avg_Ranking               
  );
  $row_count++;
  $rank_sum += $row->Avg_Ranking;
  array_push($think_big, $each_row);
}
$avg_think_big_rank = round( $rank_sum/$row_count, 2);


$live_lean_array = array();

foreach ( $live_lean as $row ) {
   $live_lean_trend_array = $row;
   if($row['avg_rank'] >= $avg_live_lean_rank) {
     $live_lean_trend_array['trend'] = 'dgreen';
   } else if($row['avg_rank']+1 >= $avg_live_lean_rank) {
     $live_lean_trend_array['trend'] = 'lgreen';
   } else {
     $live_lean_trend_array['trend'] = 'lorange';
   }
   array_push($live_lean_array, $live_lean_trend_array);
}


$think_big_array = array();
foreach ( $think_big as $row ) {
   $think_big_trend_array = $row;
   if($row['avg_rank'] >= $avg_think_big_rank) {
     $think_big_trend_array['trend'] = 'dgreen';
   } else if($row['avg_rank']+1 >= $avg_think_big_rank) {
     $think_big_trend_array['trend'] = 'lgreen';
   } else {
     $think_big_trend_array['trend'] = 'lorange';
   }
   array_push($think_big_array, $think_big_trend_array);
}


$trend_array = array('Live Lean' => $live_lean_array,
                     'Think Big' => $think_big_array
); 

print "<pre>";
print_r($trend_array);
print "</pre>";

?>
