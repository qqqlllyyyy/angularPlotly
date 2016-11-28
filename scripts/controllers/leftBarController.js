app.controller('LeftBarController', function(NavbarService) {
  var vm = this;

  vm.state = NavbarService.state;

  vm.stateList = NavbarService.stateList;

  vm.changeState = function(newState) {
    vm.state = newState;
  };

});
