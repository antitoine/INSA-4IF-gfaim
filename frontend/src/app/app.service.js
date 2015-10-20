var appServices = angular.module('gfaimApp.services', ['restangular']);

appServices
/**
 * Manage SessionStorage
 */
  .service('SessionService', ['$window', function ($window) {
    var self = this;

    self.setValue = function (key, value) {
      $window.localStorage['session.' + key] = value;
    };

    self.getValue = function (key) {
      return $window.localStorage['session.' + key];
    };

    self.destroyItem = function (key) {
      $window.localStorage.removeItem('session.' + key);
    };
  }]);