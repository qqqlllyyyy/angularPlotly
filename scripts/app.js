var app = angular.module("AngularPlotly", ["ui.router", "ngAnimate"]);

app.config(function($stateProvider, $urlRouterProvider){

  $urlRouterProvider.otherwise('/');

  $stateProvider
    .state("homepage", {
      url:"/",
      controller: "HomepageController",
      templateUrl: "views/homepage.html"
    })
    .state("bubblePlot", {
      url:"/bubble-plot",
      controller: "BubblePlotController",
      templateUrl: "views/bubblePlotHome.html"
    });

});
