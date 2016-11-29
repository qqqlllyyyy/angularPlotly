app.controller('BubblePlotController', function($http, NavbarService) {

  var vm = this;

  // NavBar Service
  vm.navBar          = NavbarService;

  vm.drawBtnBusy     = false;                // Busy when processing
  vm.secondBtnBusy   = false;                // Busy when drawing plot

  vm.geneName        = '';                   // Gene Name
  vm.yField          = '';                   // Y-axis Field Name
  vm.colorField      = '';                   // Color Field Name


  vm.firstButton     = true;                 // First Button Shown
  vm.dataFetched     = false;                // First Step of Drawing Chart is Finished
  vm.markerArea      = 'AdjustedPValue';     // Marker Area (p-value or FDR)
  vm.yDisplay        = 'top10';              // Y-Axis Diaplay Settings
  vm.colorDisplay    = 'top10';              // Color Diaplay Settings
  vm.chartWidth      = '1000';               // Chart Container Width






  vm.fieldList = ['Case_DiseaseState', 'Case_SampleSource', 'Case_CellType', 'Case_Gender'];


  // Bubble Plot Layout
  vm.bubblePlotLayout = {
    height: 800,
    //width: 1000,
    hovermode: 'closest',
    title: 'Bubble Chart',
    xaxis: {
      title: 'Log 2 Fold Change'
    },
  };


  // Fetch Data Function
  vm.refershBubblePlot = function() {

    vm.drawBtnBusy     = true;
    vm.secondBtnBusy   = true;
    vm.chartWidth      = document.getElementById('bubblePlotDiv').parentElement.offsetWidth;

    var data = {
      geneName:       vm.geneName,
      yField:         vm.yField,
      colorField:     vm.colorField,
      markerArea:     vm.markerArea,
      yDisplay:       vm.yDisplay,
      colorDisplay:   vm.colorDisplay,
      width:          vm.chartWidth,
    };

    $http
      .post("backend/bubblePlotExe.php?action=getData", data)
      .success(function(response) {
        console.log(response);
        vm.secondBtnBusy = false;
        // Response success
        if (response.message == 'Success') {
          vm.bubblePlotData = response.plotData;
          vm.bubblePlotLayout = response.layout;
          Plotly.newPlot('bubblePlotDiv', vm.bubblePlotData, vm.bubblePlotLayout);
          vm.drawBtnBusy = false;
          vm.dataFetched = true;
          vm.firstButton = false;
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
        vm.secondBtnBusy = false;
      });
  };




  vm.loadExample = function() {
    vm.geneName      = 'WASH7P';
    vm.yField        = 'Case_DiseaseState';
    vm.colorField    = 'Case_CellType';
  };






});
