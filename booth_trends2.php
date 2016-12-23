<?php
include ("connection.php");
$linkid = db_connect();
require 'conf.php';
include ("jsonwrapper/jsonwrapper.php");

error_reporting(E_ERROR);
$zone_query = "select distinct zone, type from booth";
$result = mysql_query($zone_query);

$zones = array();

while ($row = mysql_fetch_object($result)) {
    $zone = str_replace(' ', '_', $row->zone);
    $zone = strtolower($zone); 
    $zones[$zone]['zone_label'] = $row->zone;
    $zones[$zone]['zone_class'] = $zone;
    $zones[$zone]['zone_types'][$row->type] = array(); 
}

$query =  "select v.Booth_ID, b.Booth_Number, Zone, Type, Title, ROUND(avg(Informative) ,1) as Avg_Informative, "
       .  "ROUND(avg(Innovativeness), 1) as Avg_Innovativeness, ROUND(avg(Overall_Presentability),1) as "
       .  "Avg_Overall_Presentability, round((avg(Informative) + avg(Innovativeness) + avg(Overall_Presentability))/3,1) as overall_avg, "
       . " count(v.Booth_ID) as vote_count from voters v join booth b on (v.Booth_ID = b.Booth_ID) group by b.Booth_Id "
       .  "having vote_count > $rank_eligible_vote_count order by overall_avg desc";
$result = mysql_query($query);

$booth_trends = array();
while ($row = mysql_fetch_object($result)) {
   $zone = str_replace(' ', '_', $row->Zone);
   $zone = strtolower($zone); 
   array_push($zones[$zone]['zone_types'][$row->Type], array ( 
                                                 'booth_id'           => $row->Booth_ID, 
                                                 'vote_count'         => $row->vote_count, 
                                                 'booth_number'       => $row->Booth_Number,
                                                 'booth_title'        => $row->Title,
                                                 'informative_avg'    => $row->Avg_Informative,
                                                 'innovative_avg'     => $row->Avg_Innovativeness,
                                                 'presentability_avg' => $row->Avg_Overall_Presentability,
                                                 'avg_by_weightage'   => 
                                                         round($row->Avg_Informative * $weightage[$row->Type]['informative'] + 
                                                         $row->Avg_Innovativeness * $weightage[$row->Type]['innovative'] +
                                                         $row->Avg_Overall_Presentability * $weightage[$row->Type]['presentability'],1))
   );
}


$avg_scores = array();
$graph_info = array();

$sql = "select zone, count(1) as count from booth group by zone";
$rows = mysql_query($sql);
while ($row = mysql_fetch_object($rows)) {
   array_push($graph_info, array('label' => $row->zone, 'data' => $row->count)); 
}



foreach ($zones as $zone => $zone_values) {
  $zonewise_count = 0;
  foreach ($zone_values['zone_types'] as $type => $type_values) {
    $avg_score = 0;
    $score_sum = 0;
    $counter = 0;
    foreach ($type_values as $booth => $booth_values) {
      $score_sum += $booth_values['avg_by_weightage'];
      $counter++;
      $zonewise_count++;
    }
    $avg_score = $score_sum/$counter;
    $avg_scores[$zone][$type] = round($avg_score,1);
  }
  #array_push($graph_info, array( 'label' => $zone, 'data' => $zonewise_count ));
}


$zone_arr = array();
foreach ($zones as $key => &$values) {
   foreach ($values['zone_types'] as $type_name => &$type_value) {
     foreach ($type_value as &$booth) {
       if( $booth['avg_by_weightage'] >= $avg_scores[$key][$type_name] ) {
         $booth['trend_color'] = 'dgreen';
       } else if($booth['avg_by_weightage']+1 >= $avg_scores[$key][$type_name]) {
         $booth['trend_color'] = 'lgreen';
       } else{
         $booth['trend_color'] = 'lorange';
       }
     }
   }
   array_push($zone_arr, $values);
}

$booth_list = array ( 'conf_location' => 'Kolkata', 
                      'zones'         => $zone_arr, 
                      'avg_scores'    => $avg_scores,
                      'graph_info'    => $graph_info);

echo json_encode($booth_list );

?>
