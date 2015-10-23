gfaimApp.controller('welcomeCtrl', ['$scope', 'searchService', '$log', '$state',
    function ($scope, searchService, $log, $state) {

        $scope.isSearching = false;

        $scope.search = function () {
            $scope.isSearching = true;
            searchService.search($scope.query)
        };

    }]);


