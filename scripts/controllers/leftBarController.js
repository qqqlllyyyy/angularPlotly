app.controller('LeftBarController', function() {
  var vm = this;

  vm.state = 'homepage';

  vm.stateList = [
    {'link': 'homepage', 'name': 'Home', 'icon': 'fa-home'},
    {'link': 'bubblePlot', 'name': 'Bubble Plot', 'icon': 'fa-dot-circle-o'}
  ];

  vm.changeState = function(newState) {
    vm.state = newState;
  }

});
