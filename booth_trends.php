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
                                                 'booth_title'        => truncateString($row->Title),
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
$avg_votes = array();
$graph_info = array();


$sql = "select zone, count(1) as count from booth group by zone";
$rows = mysql_query($sql);
while ($row = mysql_fetch_object($rows)) {
   array_push($graph_info, array('label' => $row->zone, 'data' => $row->count)); 
}

foreach ($zones as $zone => $zone_values) {
  $zonewise_count = 0;
  foreach ($zone_values['zone_types'] as $type => $type_values) {
    $avg_score = $avg_vote = 0;
    $score_sum = $vote_sum = 0;
    $counter = 0;
    foreach ($type_values as $booth => $booth_values) {
      $score_sum += $booth_values['avg_by_weightage'];
      $vote_sum += $booth_values['vote_count'];
      $counter++;
      $zonewise_count++;
    }
    $avg_score = $score_sum/$counter;
    $avg_scores[$zone][$type] = round($avg_score,1);
    $avg_vote = $vote_sum/$counter;
    $avg_votes[$zone][$type] = round($avg_vote);
  }
  #array_push($graph_info, array( 'label' => $zone, 'data' => $zonewise_count ));
}

$zone_arr = array();
foreach ($zones as $key => &$values) {
   foreach ($values['zone_types'] as $type_name => &$type_value) {
     $both_above_midpoint = array();
     $one_above_midpoint  = array();
     $both_below_midpoint = array();
     foreach ($type_value as &$booth) {

       if ($booth['vote_count'] >= $avg_votes[$key][$type_name] && 
           $booth['avg_by_weightage'] >= $avg_scores[$key][$type_name]) { 
             $booth['trend_color'] = 'trend1';
             array_push($both_above_midpoint, $booth);
       } else if($booth['vote_count'] >= $avg_votes[$key][$type_name] || 
           $booth['avg_by_weightage'] >= $avg_scores[$key][$type_name]) {
             $booth['trend_color'] = 'trend2';
             array_push($one_above_midpoint, $booth);
       } else {
            $booth['trend_color'] = 'trend3';
            array_push($both_below_midpoint, $booth);
       }

     }
     $type_value = array_merge($both_above_midpoint, $one_above_midpoint, $both_below_midpoint );
   }
   array_push($zone_arr, $values);
}





$stats = stats();
$booth_list = array ( 'conf_location' => 'Chennai', 
                      'zones'         => $zone_arr, 
                      'avg_scores'    => $avg_scores,
                      'graph_info'    => $graph_info,
                      'stats'         => $stats);

function stats() {
  $sql = "select count(1) as booth_count from booth"; 
  $result = mysql_query($sql);
  $booth_count = mysql_fetch_object($result);
  $booth_count = $booth_count->booth_count;

  $sql = "select count(1) as voters_count from voters"; 
  $result = mysql_query($sql);
  $voters_count = mysql_fetch_object($result);
  $voters_count = $voters_count->voters_count;


  $sql = "select count(distinct signum_id) as voted_user_count from voters"; 
  $result = mysql_query($sql);
  $voted_user_count = mysql_fetch_object($result);
  $voted_user_count = $voted_user_count->voted_user_count;


  $sql = "select SPOC from booth"; 
  $result = mysql_query($sql);
  $users = array();
  while($row = mysql_fetch_object($result)) {
    if(preg_match("/,/", $row->SPOC)) {
      $SPOCs = explode(',', $row->SPOC);
    } else {
      $SPOCs = explode(';', $row->SPOC);
    }
    foreach ($SPOCs as $user) {
      array_push($users, $user);
    }
  }

   return array('booth_count'  => $booth_count, 
                'votes_casted' => $voters_count,
                'voted_user_count' => $voted_user_count,
                'participant_count' => count($users));

}


function truncateString($string) {
    if (strlen($string) > 50) {
        #$string = substr($string, 0, 50) . "...";
        $string = substr($string, 0, strrpos(substr($string, 0, 50), ' '));
    }
    return $string;
}

echo json_encode($booth_list );

?>
