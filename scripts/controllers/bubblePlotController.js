app.controller('BubblePlotController', function($http) {

  var vm = this;

  // Error message handling
  vm.errorMsg = '';
  vm.showErrorMsg = false;

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

        if (response.message != 'Error') {
          vm.bubblePlotData = response.plotData;
          Plotly.newPlot('bubblePlotDiv', vm.bubblePlotData, vm.bubblePlotLayout);
        } else {
          vm.errorMsg = response.messageDetail;
          //vm.errorMsg = 'aa';
          vm.showErrorMsg = true;
        }

      })
      .error(function(error) {
        console.log(error);
        vm.errorMsg = error;
        vm.showErrorMsg = true;
      });


  };

});
