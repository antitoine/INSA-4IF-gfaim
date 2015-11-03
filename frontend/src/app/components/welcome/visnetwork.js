gfaimApp
    .directive('visNetwork', function() {
    return {
        restrict: 'E',
        scope: {
            onSelect: '&',
            network: '='
        },
        link: function($scope, $element, $attrs) {

            var onSelect = $scope.onSelect() || function(prop) {};
            $scope.network.on('select', function(properties) {
                onSelect(properties);
            });

        }

    }
});