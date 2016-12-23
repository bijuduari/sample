var app = angular.module("Confluence", []);

app.controller("ZoneCtrl", function($scope, $http) {
  $http.get('booth_trends.php').
    success(function(data, status, headers, config) {
      $scope.zones = data.zones ;
      $scope.conf_location = data.conf_location;
      $scope.avg_scores = data.avg_scores;
      $scope.graph_info = data.graph_info;
      $scope.stats = data.stats;
      populateGraph(data.graph_info);
    }).
    error(function(data, status, headers, config) {
      // log error
    });
});


function populateGraph(graph_info) {
  var data = [];
  var color_code = { 'Live Lean' : '#F36868','Think Big' : '#AE77EF', 'Go Green': '#26CF2D'};
  var i=0;
  $(graph_info).each(function(index,value) {
    var type_color = color_code[value.label];
    data.push({'label': value.label, 'data' : value.data, 'color': type_color});
     i++;
  });

      var placeholder = $('#stats');
      if(placeholder.length == 1) {
      $.plot(placeholder, data, {
          series: {
              pie: { 
                  show: true,
                  label: {
                    show: true,
                   radius: 2 / 4,
                   formatter: function (label, series) {
                        return '<div style="font-size:10pt;padding:2px;color:white;">' + label + '<br/>' + series.data[0][1] + '</div>';

                    },
                    threshold: 0.1
                  }
              }
          },
          legend: {
            show: false,
            container: $('#legend')
          }
      });
      }

}

