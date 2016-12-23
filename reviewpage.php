<?php 
include ("connection.php");
$linkid = db_connect();
session_start();
error_reporting(E_ERROR);


if(! array_key_exists('signum',$_SESSION)){
  header("location:login.php?redir=review");
   exit;
}

$jury_list = array('efgjkmp', 'ebceffg', 'evyzaca', 'evemvey', 
                   'eprkgua', 'ebinkur', 'eancsha', 'enejsil', 
                   'esapati', 'eboskas', 'emohvij', 'eramgop',
		   'ekrinai', 'eimnpqj', 'ebijdua');


if(! in_array(strtolower($_SESSION['signum']), $jury_list )) {
  header("location:login.php");
   exit;
}

error_log('Jury : ' . $_SESSION['login_name'] . ' Currently accessing review page to update the details. ');

$action = $_REQUEST['_action'];
$message = '';

if ($action == 'update_score') {
   if(update_score()) {
    $message = 'Data Saved Successfully.';
   } else {
    $message = 'Error Saving Data.';
   }
}

function update_score() {
   $booth_id     = $_REQUEST['booth_id'];
   $signum       = $_SESSION['signum'];
   $jury_score   = $_REQUEST['jury_score'];
   
   $jury_score = round($jury_score, 1);
   if (! $booth_id || ! $signum ) {
     return false;
   }

   #See if record already exist
   $query = "select count(1) as booth_count from jury_score where booth_id=$booth_id";
   $result = mysql_query($query);
   $row = mysql_fetch_object($result);
   error_log($query);
   $data_exists = $row->booth_count;

   error_log('Data Exists : ' . $data_exists);
   if ($data_exists > 0) {
      #updte the row
      $update_sql = "update jury_score set jury_score='$jury_score', signum='$signum' where booth_id=$booth_id";
      error_log('Update : ' . $update_sql);
      mysql_query($update_sql);

   } else {
      #Insert a row
      $insert_sql = "insert into jury_score(booth_id, signum, jury_score) "
                  . "values($booth_id, '$signum', '$jury_score')";
      error_log('Insert : ' . $insert_sql);
      mysql_query($insert_sql);
   }

}


?>

<!DOCTYPE html>
<html lang="en" ng-app='Confluence'>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--<link rel="icon" href="../../favicon.ico">-->

  <title>Confluence 3 Dashboard</title>
  <!-- Bootstrap core CSS -->
  <link href="bootstrap.css" rel="stylesheet">
  <link href="dashboard.css" rel="stylesheet">
  <link href="tablesort.css" rel="stylesheet">
  <script src="jquery-1.10.2.min.js"></script>
  <script src="bootstrap.min.js"></script>
  <script src="angular.min.js"></script>
  <script src="angular-tablesort.js"></script>
  <script src="review.js"></script>

  <style type='text/css'>
    .trend1 { background-color: #459F49 !important; color: #000 !important; }
    .trend2, .trend3 { color: #000; }
  </style>

</head>
<body>
  <div ng-controller="ZoneCtrl"  class='col-sm-12 col-md-12 col-lg-12' style='margin:auto 0px;float:none'>
  <div class='col-md-12'><a class="btn btn-primary col-md-1 pull-right" href="logout.php" role="button">Logout</a></div>

        <div ng-repeat="zone in zones" class="item" align='center'>
           <h3 class='ericsson-sfont {{zone.zone_class}}'>{{zone.zone_label}} Zone</h3>
           <div ng-repeat="(key, value) in zone.zone_types" class="item" align='center'>
             <h4 class='ericsson-sfont {{zone.zone_class}}'>{{key}} Bays</h4>
             <table class='table table-striped {{zone.zone_class}}' ts-wrapper>
               <tbody>
                 <tr><th colspan=4>Midpoint for Vote = {{avg_votes[zone.zone_class][key]}}</th><th colspan=5>Midpoint for Score = {{avg_scores[zone.zone_class][key]}}</th></tr>
                 <tr>
                 <th ts-criteria="booth_number">Booth Number</th>
                 <th ts-criteria="booth_title">Title</th>
                 <th ts-criteria="vote_count|parseInt"># Votes</th>
                 <th ts-criteria="informative_avg|parseFloat">Informative</th>
                 <th ts-criteria="innovative_avg|parseFloat">Innovative</th>
                 <th ts-criteria="presentability_avg|parseFloat">Presentability</th>
                 <th ts-criteria="avg_by_weightage|parseFloat">Overal Weightage</th>
                 <th ts-criteria="jury_score|parseFloat">Jury Score</th>
                 <th ts-criteria="final_score|parseFloat">Final Score</th></tr>
                 <tr ng-repeat="row in value" ts-repeat class='{{row.trend_color}}'>
                   <td class='talign-center'>{{row.booth_number}}</td>
                   <td>{{row.booth_title}}</td>  
                   <td class='talign-center'>{{row.vote_count}}</td> 
                   <td class='talign-center'>{{row.informative_avg}}</td>
                   <td class='talign-center'>{{row.innovative_avg}}</td>
                   <td class='talign-center'>{{row.presentability_avg}}</td>  
                   <td class='talign-center' id='{{row.booth_id}}_avg_score'>{{row.avg_by_weightage}}</td>
                   <td class='talign-center'><input type='text' class='jury_score' 
                            id='{{row.booth_id}}' name='jury_score' type='number' size=2 value='{{jury_scores[row.booth_id]}}' onBlur="saveScore(this)"></td>
                   <td class='talign-center' id='{{row.booth_id}}_final_score'>{{final_score(row.avg_by_weightage, jury_scores[row.booth_id])}}</td>
                 </tr>
               </tbody>
             </table>
           </div>
        </div>
  </div>
</body>
</html>

