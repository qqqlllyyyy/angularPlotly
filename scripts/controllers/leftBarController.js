app.controller('LeftBarController', function(NavbarService) {
  var vm = this;

  vm.leftBar = NavbarService;

  vm.stateList = NavbarService.stateList;

  vm.changeState = function(newState) {
    NavbarService.changeState(newState);
    vm.state = NavbarService.state;
  };

});
