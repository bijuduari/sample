<?php

$midpoint_vote = 25;
$midpoint_score = 3.7;
$zones = 

Array (
  Array (
    'vote' => 10,
    'score' => 4.7
  ),
  Array (
    'vote' => 33,
    'score' => 2.7
  ),
  Array (
    'vote' => 20,
    'score' => 3.1
  ),
  Array (
    'vote' => 30,
    'score' => 4.1
  ),
  Array (
    'vote' => 35,
    'score' => 4.4
  ),
  Array (
    'vote' => 40,
    'score' => 3.7
  )
);

$score = array();
$vote = array();

$both_above_midpoint = array();
$one_above_midpoint = array();
$both_below_midpoint = array();

foreach ($zones as $key => $zone) {
   if ($zone['vote'] >= $midpoint_vote && $zone['score'] >= $midpoint_score) { 
     array_push($both_above_midpoint, $zone);
   } else if($zone['vote'] >= $midpoint_vote || $zone['score'] >= $midpoint_score) {
     array_push($one_above_midpoint, $zone);
   } else {
     array_push($both_below_midpoint, $zone);
   }
}

$zones = array_merge($both_above_midpoint, $one_above_midpoint, $both_below_midpoint);

print_r($zones);

?>
