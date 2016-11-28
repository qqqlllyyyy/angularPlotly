app.controller('BubblePlotController', function($http, NavbarService) {

  var vm = this;

  // NavBar Service
  vm.navBar = NavbarService;

  vm.drawBtnBusy = false;

  vm.geneName = '';
  vm.yField = '';
  vm.colorField = '';

  vm.fieldList = ['Case_DiseaseState', 'Case_SampleSource', 'Case_CellType', 'Case_Gender'];


  // Bubble Plot Layout
  vm.bubblePlotLayout = {
    title: 'Marker Size and Color',
    showlegend: false,
    height: 400,
    width: 480
  };


  // Draw Function
  vm.refershBubblePlot = function() {
    vm.drawBtnBusy = true;
    var data = {
      geneName: vm.geneName,
    };
    $http
      .post("backend/bubblePlotExe.php?action=getData", data)
      .success(function(response) {
        console.log(response);
        // Response success
        if (response.message == 'Success') {
          vm.bubblePlotData = response.plotData;
          Plotly.newPlot('bubblePlotDiv', vm.bubblePlotData, vm.bubblePlotLayout);
          vm.drawBtnBusy = false;
        }
        // Response failed
        else {
          vm.drawBtnBusy = false;
          bootbox.alert({
            title: "Error",
            message: response.messageDetail,
            className: 'modal-error',
            buttons: {
              ok: {
                  label: 'Got It!',
              }
            },
          });
        }
      })
      .error(function(error) {
        console.log(error);
        vm.errorMsg = error;
        vm.showErrorMsg = true;
      });

  };

});
