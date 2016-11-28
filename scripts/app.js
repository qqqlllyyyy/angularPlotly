var app = angular.module("AngularPlotly", ["ui.router", "ngAnimate", "ngMaterial"]);

app.config(function($stateProvider, $urlRouterProvider){

  $urlRouterProvider.otherwise('/');

  $stateProvider
    .state("homepage", {
      url:"/",
      controller: "HomepageController as homepageCtrl",
      templateUrl: "views/homepage.html"
    })
    .state("bubblePlot", {
      url:"/bubble-plot",
      controller: "BubblePlotController as bubblePlotCtrl",
      templateUrl: "views/bubblePlotHome.html"
    })
    .state("volcanoPlot", {
      url:"/volcano-plot",
      controller: "VolcanoPlotController as volcanoPlotCtrl",
      templateUrl: "views/volcanoPlotHome.html"
    })
    .state("UIElements", {
      url:"/UI-elements",
      controller: "UIElementsController as UIElementsPlotCtrl",
      templateUrl: "views/UIElements.html"
    });

});






app.controller('AppCtrl', function($scope) {
  $scope.title1 = 'Button';
  $scope.title4 = 'Warn';
  $scope.isDisabled = true;

  $scope.googleUrl = 'http://google.com';
});
