app.controller('BubblePlotController', function($http) {

  var vm = this;

  vm.text = '';

  vm.geneName = 'WASH7P';

  vm.bubblePlotData = [{
    x: [1, 2, 3, 4],
    y: [10, 11, 12, 13],
    mode: 'markers',
    // marker: {
    //   color: ['rgb(93, 164, 214)', 'rgb(255, 144, 14)',  'rgb(44, 160, 101)', 'rgb(255, 65, 54)'],
    //   opacity: [1, 0.8, 0.6, 0.4],
    //   size: [40, 60, 80, 100]
    // }
  }];


  // Bubble Plot Layout
  vm.bubblePlotLayout = {
    title: 'Marker Size and Color',
    showlegend: false,
    height: 400,
    width: 480
  };

  // Function
  vm.refershBubblePlot = function() {

    var data = {
      geneName: vm.geneName,
    };

    $http
      .post("backend/bubblePlotExe.php?action=getData", data)
      .success(function(response) {
        console.log(response);
        vm.bubblePlotData = response;
        Plotly.newPlot('bubblePlotDiv', vm.bubblePlotData, vm.bubblePlotLayout);
      })
      .error(function(error) {
        console.log(error);
      });


  };

});
