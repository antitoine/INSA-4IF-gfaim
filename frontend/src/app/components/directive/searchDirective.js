gfaimApp.directive('searchResult', function () {
    return {
        templateUrl: 'app/components/directive/searchDirective.html',
        replace: true,
        transclude: true,
        scope: {
            title: '@',
            img: '@',
            mainDescription: '@'
        },
        link: function (scope, element, attrs) {},
        controller: function ($scope) {}
    };
});