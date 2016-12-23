<?php
include ("connection.php");
$linkid = db_connect();
require 'conf.php';
include ("jsonwrapper/jsonwrapper.php");

error_reporting(E_ERROR);

$zones = array();
$zones['conf_location'] = 'Kolkata';

$zones['zone_types']['Idea']     = array(); 
$zones['zone_types']['Demo']     = array(); 
$zones['zone_types']['Creative'] = array(); 
$zones['zone_types']['Knowledge'] = array(); 


$query1 = "select b.Zone, count(1) as zonewise_count from voters v join booth b on (b.booth_id = v.booth_id) group by zone";
$result1 = mysql_query($query1);
$query2 = "select b.Type, count(1) as typewise_count from voters v join booth b on (b.booth_id = v.booth_id) group by b.Type";
$result2 = mysql_query($query2);
$query3 = "select date_format(Updated_Time, '%H') as hourwise, count(1) as timewise_count from voters "
        . "where date_format(Updated_Time, '%d') ='09' group by hourwise having "
        . "timewise_count > 0 order by hourwise limit 10";
$result3 = mysql_query($query3);


$zonewise_vote_count = array();
while ($row = mysql_fetch_object($result1)) {
   array_push($zonewise_vote_count, array ( 
                                       'label' => $row->Zone,
                                       'data'     => $row->zonewise_count
                                    )
   );
}

$typewise_vote_count = array();
while ($row = mysql_fetch_object($result2)) {
   array_push($typewise_vote_count, array ( 
                                       'label' => $row->Type,
                                       'data'     => $row->typewise_count
                                    )
   );
}


$hourwise_vote_count = array();
while ($row = mysql_fetch_object($result3)) {
   array_push($hourwise_vote_count, array ( 
                                       'label' => $row->hourwise,
                                       'data'     => $row->timewise_count
                                    )
   );
}
$stats = array ( 'zonewise_count' => $zonewise_vote_count, 
                 'typewise_count' => $typewise_vote_count,
                 'timewise_count' => $hourwise_vote_count,
);

echo json_encode($stats);
?>
