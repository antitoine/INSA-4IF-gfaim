"use strict";

gfaimApp.config(['stateHelperProvider', '$urlRouterProvider', function (stateHelperProvider, $urlRouterProvider) {

  stateHelperProvider

    .state({
      name: 'welcome',
      url: "/",
      templateUrl: "/app/components/welcome/welcome.html",
      controller: "welcomeCtrl"
    })
    .state({
      name: 'search',
      url: "/search",
      abstract: 'true',
      template: '<ui-view/>',
      children: [
        {
          name: 'results',
          url: "/results",
          templateUrl: '/app/components/search/results.html',
          controller: 'resultsCtrl'
        }
      ]
      })
    .state({
      name: 'otherwise',
      url: "*path",
      template: "",
      controller: [
        '$state',
        function($state) {
          $state.go('welcome')
        }]
    })

}]);
