"use strict";


var gfaimApp = angular.module('gfaimApp', [
    'ui.router',
    'ui.router.stateHelper',
    'restangular',
    'ui.bootstrap',
    'angular-loading-bar',
    'ui-notification',
    'ui.bootstrap-slider'
])

    .config(['RestangularProvider', function (RestangularProvider) {
        RestangularProvider.setBaseUrl(config.api.url);
    }])

    .run(['Restangular', '$http', '$state', 'Notification', '$rootScope', '$log', '$timeout', start]);

function start(Restangular, $http, $state, Notification, $rootScope, $log, $timeout) {

    Restangular.addResponseInterceptor(function (data, operation, what, url, response, deferred) {
        // log every response from the server in debug (filter )
        console.debug(data);
        return data;
    });


    /**
     * Intercepte les erreurs du serveur
     */
    Restangular.setErrorInterceptor(
        function (response) {
            // https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
            if(response.status !== -1){
                Notification.error("Erreur HTTP : " + response.status);
            }
            return true;
        }
    );

    $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {
        // intercepte un changement de route
    });

}

