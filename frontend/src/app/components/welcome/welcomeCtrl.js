gfaimApp.controller('welcomeCtrl', ['$scope', 'searchService', '$log', '$state',
    function ($scope, searchService, $log, $state) {

        $scope.isSearching = false;
        $scope.results=[];

        $scope.search = function () {
            $scope.isSearching = true;
            searchService.search($scope.query)
                .then(function(result){
                    $scope.results.length = 0;
                    $scope.results = result;
                })
        };

    }]);


