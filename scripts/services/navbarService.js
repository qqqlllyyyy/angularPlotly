app.service('NavbarService', function($state, $location) {

  var vm = this;

  vm.path = $location.path();

  switch(vm.path) {
    case '/':
      vm.state = 'homepage';
      break;
    case '/bubble-plot':
      vm.state = 'bubblePlot';
      break;
    case '/volcano-plot':
      vm.state = 'volcanoPlot';
      break;
    case '/UI-elements':
      vm.state = 'UIElements';
      break;
    default:
      vm.state = 'test';
  }

  vm.stateList = [
    {'link': 'homepage', 'name': 'Home', 'icon': 'fa-home'},
    {'link': 'bubblePlot', 'name': 'Bubble Plot', 'icon': 'fa-dot-circle-o'},
    {'link': 'volcanoPlot', 'name': 'Volcano Plot', 'icon': 'fa-line-chart'},
    {'link': 'UIElements', 'name': 'UI Elements', 'icon': 'fa-flask'},
  ];

  vm.changeState = function(newState) {
    vm.state = newState;
    $state.go(newState);
  };

});
