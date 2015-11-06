gfaimApp.directive('searchResult', function () {
    return {
        templateUrl: 'app/components/directive/searchDirective.html',
        replace: true,
        transclude: true,
        scope: {
            title: '@',
            img: '@',
            mainDescription: '@',
            wiki: '@',
            caption:'@',
            words : '@'
        },
        link: function (scope, element, attrs) {

            scope.$watch('img', function() {
                var myHilitor = new Hilitor('result');
                myHilitor.apply(scope.words);
            });

        },
        controller: function ($scope) {}
    };
});

gfaimApp.directive('actualSrc', function () {
    return{
        link: function postLink(scope, element, attrs) {
            attrs.$observe('actualSrc', function(newVal, oldVal){
                if(newVal != undefined){
                    var img = new Image();
                    img.src = attrs.actualSrc;
                    angular.element(img).bind('load', function () {
                        element.attr("src", attrs.actualSrc);
                    });
                }
            });

        }
    }
});