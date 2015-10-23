gfaimApp.controller('welcomeCtrl', ['$scope', 'searchService', '$filter', '$log', '$state',
    function ($scope, searchService, $filter, $log, $state) {

        $scope.isSearching = false;

        $scope.search = function () {
            //$state.go("search.results")
            $scope.isSearching = true;
        };

    }]);


