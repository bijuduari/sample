var app = angular.module("Confluence", ['tableSort']);

app.controller("ZoneCtrl", function($scope, $http) {
  $http.get('juryreview.php').
    success(function(data, status, headers, config) {
      $scope.zones = data.zones ;
      $scope.conf_location = data.conf_location;
      $scope.avg_scores = data.avg_scores;
      $scope.avg_votes = data.avg_votes;
      $scope.jury_scores = data.jury_scores;
      $scope.graph_info = data.graph_info;
      populateGraph(data.graph_info);
      $scope.final_score = function(avg_weightage, jury_score) {
         if(jury_score > 0 ) {
           var final_score= (parseFloat(avg_weightage) + parseFloat(jury_score))/2;
           return final_score.toFixed(2);
         } else {
           return 0;
         }
      }
    }).
    error(function(data, status, headers, config) {
      // log error
    });
});


function populateGraph(graph_info) {
  var data = [];
  var color = ['#F9A12F','#F26341','#3EB9E0'];
  var i=0;
  $(graph_info).each(function(index,value) {
    data.push({'label': value.label, 'data' : value.data, 'color': color[i]});
     i++;
  });

      var placeholder = $('#stats');
      if(placeholder.length == 1) {
      $.plot(placeholder, data, {
          series: {
              pie: { 
                  show: true
              }
          },
          legend: {
            show: false
          }
      });
      }


}

function saveScore(element) {
  var booth_id = $(element)[0].id;
  var new_score = $(element).val();
  var old_score = $(element)[0].defaultValue;
  if ( old_score != new_score ) {
    $.ajax({
       type: 'POST',
       url: 'reviewpage.php',
       success: function() {  
           var final_score_el = $('#' +booth_id + '_final_score');
           var avg_score = $('#' +booth_id + '_avg_score');
           avg_score = avg_score.html();
           final_score = (parseFloat(avg_score) + parseFloat(new_score))/2; 
           final_score_el.html(final_score.toFixed(2));
      } ,
       data: {'booth_id' : booth_id, 'jury_score' : new_score, '_action' : 'update_score' }
    });
  }
  
}

