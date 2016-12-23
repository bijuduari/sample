<?php
include ("connection.php");
$linkid = db_connect();
include ("jsonwrapper/jsonwrapper.php");

error_reporting(E_ERROR);

$locations = array('kolkata', 'pune', 'gurgaon', 'noida', 'bangalore', 'chennai');

$locationwise_data = array();

foreach ( $locations as $location) {
   $query =  "select b.zone, count(1) as vote_count from ". $location. "_voters v join ". $location. "_booth b on "
          .  " ( b.booth_id = v.booth_id) group by b.zone";
   $result = mysql_query($query);

   while ($row = mysql_fetch_object($result)) {
     $locationwise_data[$location][$row->zone] =  intval($row->vote_count);  
   }
}

$zonewise_voting_info = array();
array_push($zonewise_voting_info, array('Location', 'Live Lean', 'Think Big', 'Go Green'));


$locationwise_voting_info = array();
array_push($locationwise_voting_info, array('Location', 'Vote Count' ));


foreach ($locationwise_data as $key => $value) {
  array_push($zonewise_voting_info, array( $key, $value['Live Lean'], $value['Think Big'], $value['Go Green'])); 
  array_push($locationwise_voting_info, array($key, intval($value['Live Lean']) + 
                                                    intval($value['Think Big']) + 
                                                    intval($value['Go Green'])
                                         )
  );
}

$categorywise_data = array();
$hourwise_data = array();

foreach ( $locations as $location) {
   $query =  "select b.Type, count(1) as vote_count from ". $location. "_voters v join ". $location. "_booth b on "
          .  " ( b.booth_id = v.booth_id) group by b.Type";
   $result = mysql_query($query);

   while ($row = mysql_fetch_object($result)) {
     $categorywise_data[$location][$row->Type] =  intval($row->vote_count);  
   }


   $query2 = "select date_format(Updated_Time, '%H') as hour, count(1) as count from "
           . $location . "_voters group by date_format(Updated_Time, '%H') having count(hour) > 10 "
           . " and hour in ('10', '11', '12', '13', '14') ";
   $result2 = mysql_query($query2);
   while ($row2 = mysql_fetch_object($result2)) {
     $hourwise_data[$location][$row2->hour] =  intval($row2->count);  
   }
}

$hourwise_voting_info = array();
array_push($hourwise_voting_info, array('Location', '10:00 - 11:00', '11:00 - 12:00', '12:00 - 13:00', '13:00 - 14:00', '14:00 - 15:00'));

foreach ($hourwise_data as $key => $value) {
  array_push($hourwise_voting_info, array($key, $value['10'], $value['11'], $value['12'], $value['13'], $value['14']));
}

$categorywise_voting_info = array();
array_push($categorywise_voting_info, array('Location', 'Demo', 'Idea', 'Creative'));

foreach ($categorywise_data as $key => $value) {
  array_push($categorywise_voting_info, array($key, $value['Demo'], $value['Idea'], $value['Creative']));
}

?>

<!DOCTYPE html>
<html>
  <head>
    <script src="jquery-1.10.2.min.js"></script>
    <link href="bootstrap.css" rel="stylesheet">
    <link href="simple-sidebar.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  </head>

  <body>
    <!-- Sidebar -->
    <div id="sidebar-wrapper" class='col-md-3'>
      <ul class="sidebar-nav">
        <li class="sidebar-brand" class='report'>
            <a href="#">Reports</a>
        </li>
        <li id='locationwise' class='report'>
            <a href="#" >Locationwise</a>
        </li>
        <li id='zonewise' class='report'>
            <a href="#">Zonewise</a>
        </li>
        <li id='categorywise' class='report'>
            <a href="#">Categorywise</a>
        </li>
        <li id='timewise' class='report'>
            <a href="#">Timewise</a>
        </li>
      </ul>
    </div>
    <div class='col-md-9'>
    <div id="locationwise_chart" class='chart' style="width: 940px; height: 600px;" ></div>
    <div id="zonewise_chart" class='chart' style="width: 940px; height: 600px;" ></div>
    <div id="categorywise_chart" class='chart' style="width: 940px; height: 600px;" ></div>
    <div id="timewise_chart" class='chart' style="width: 980px; height: 650px;" ></div>
    </div>
     <script type='text/javascript'>
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawVisualization);
      
      function drawVisualization() {
        var zonewise_graph_data = <?php echo json_encode($zonewise_voting_info); ?>;
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable(zonewise_graph_data);

        var options = {
          title : 'Zonewise Vote Count',
          vAxis: {title: "Vote Count"},
          hAxis: {title: "Location"},
          seriesType: "bars",
          series: {5: {type: "line"}}
        };
      
        var chart = new google.visualization.ComboChart(document.getElementById('zonewise_chart'));
        chart.draw(data, options);

        var locationwise_graph_data = <?php echo json_encode($locationwise_voting_info); ?>;
        var data2 = google.visualization.arrayToDataTable(locationwise_graph_data);

        var options2 = {
           title: "Locationwise Voting Info",
           vAxis: {title: "Vote Count"},
           hAxis: {title: "Location"},
           bar: {groupWidth: "75%"},
            legend: { position: "none" }
         };
        var chart2 = new google.visualization.ColumnChart(document.getElementById('locationwise_chart'));
        chart2.draw(data2, options2);

        var categorywise_graph_data = <?php echo json_encode($categorywise_voting_info); ?>;
        var data3 = google.visualization.arrayToDataTable(categorywise_graph_data);

        var options3 = {
          title : 'Categorywise Vote Count',
          vAxis: {title: "Vote Count"},
          hAxis: {title: "Location"},
          seriesType: "bars",
          series: {5: {type: "line"}}
        };
      
        var chart3 = new google.visualization.ComboChart(document.getElementById('categorywise_chart'));
        chart3.draw(data3, options3);

        var locationwise_graph_data = <?php echo json_encode($locationwise_voting_info); ?>;
        var data2 = google.visualization.arrayToDataTable(locationwise_graph_data);

        var options4 = {
          title: 'Hourwise Voting across location',
          hAxis: {
            title: 'Vote Count',
            minValue: 0,
          },
          vAxis: {
            title: 'Location'
          },
          bars: 'horizontal'
        };


        var timewise_graph_data = <?php echo json_encode($hourwise_voting_info); ?>;
        var data4 = google.visualization.arrayToDataTable(timewise_graph_data);
        var chart4 = new google.visualization.BarChart(document.getElementById('timewise_chart'));
        chart4.draw(data4, options4);
      }
     
      $('.report').click(function(ev, index) {
        var div_id = $(this)[0].id;
        $('.chart').hide();
        $('#'+ div_id + '_chart').show('slow');
      });


     </script>

  </body>

</html>
