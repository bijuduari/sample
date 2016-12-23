<?php
include ("connection.php");
$linkid = db_connect();
require 'conf.php';
include ("jsonwrapper/jsonwrapper.php");

error_reporting(E_ERROR);

$zones = array();
$zone_label = (isset($_GET['zone'])) ? $_GET['zone']  :  'Live Lean';

$zone = str_replace(' ', '_', $zone_label);
$zone = strtolower($zone); 

$zones['conf_location'] = 'Kolkata';
$zones['zone_label'] = $zone_label;
$zones['zone_class'] = $zone;

$zones['zone_types']['Idea']     = array(); 
$zones['zone_types']['Demo']     = array(); 
$zones['zone_types']['Creative'] = array(); 
$zones['zone_types']['Knowledge'] = array(); 

$query =  "select Booth_ID, Booth_Number, Title, Description, Zone, Type, domain, floor from  booth where Zone='$zone_label'";
error_log($query);
$result = mysql_query($query);

$booth_trends = array();
while ($row = mysql_fetch_object($result)) {
   array_push($zones['zone_types'][$row->Type], array ( 
                                                 'booth_id'       => $row->Booth_ID, 
                                                 'booth_number'   => $row->Booth_Number,
                                                 'domain'         => $row->domain,
                                                 'description'    => $row->Description,
                                                 'floor'          => $row->floor,
                                                 'booth_title'    => $row->Title)
   );
}

function truncateString($string) {
  return $string;
  if (strlen($string) > 50) {
      #$string = substr($string, 0, 50) . "...";
      $string = substr($string, 0, strrpos(substr($string, 0, 50), ' '));
  }
  return $string;
}

echo json_encode($zones );

?>
