gfaimApp.controller('welcomeCtrl', ['$scope', 'searchService', '$log', '$state', '$timeout', '$q',
    function ($scope, searchService, $log, $state, $timeout, $q) {

        $scope.isSearching = false;
        $scope.searchingInProcess = false;
        $scope.searchingDone = false;
        $scope.results = [];
        $scope.isCollapsed = true;
        $scope.loadingText = "Loading ...";
        $scope.loadingSubText = "This could take a while";

        $scope.sliders = {
            similarity: {
                sliderValue: 0.5,
                min: 0,
                step: 0.2,
                max: 5,
                value: 0.5
            },
            confidence: {
                sliderValue: 1,
                min: 0,
                step: 0.2,
                max: 2,
                value: 1
            },
            nbPages: {
                sliderValue: 20,
                min: 2,
                step: 2,
                max: 20,
                value: 20
            }
        };

        function iterateSearch(index) {
            var searchWords =
            {
                3: "Still loading ???",
                2: "This is boring.",
                1: "At least, it takes less time than cooking pastas...",
                0: "Please !!!"
            };
            var searchWords2 =
            {
                3: "",
                2: "",
                1: "and a pizza",
                0: "I am starving"
            };
            $timeout(function () {
                if (index >= 0) {
                    $scope.loadingText = searchWords[index];
                    $scope.loadingSubText = searchWords2[index];
                    index = index - 1;
                    iterateSearch(index)
                }
            }, 10000);

        }

        $scope.nodes = new vis.DataSet();
        $scope.edges = new vis.DataSet();

        //      var index = 5;


        $scope.network_data = {
            nodes: $scope.nodes,
            edges: $scope.edges
        };

        $scope.network_options = {
            "edges": {
                "smooth": {
                    "forceDirection": "none"
                }
            },
            "interaction": {
                "hover": true
            },
            "physics": {
                "minVelocity": 0.75
            }
        };

        var container = document.getElementById('container-network');
        $scope.network = new vis.Network(container, $scope.network_data, $scope.network_options || {});

        var ind = 500;

        $scope.resetGraph = function () {
            $scope.nodes.clear();
            $scope.edges.clear();
        };

        $scope.onNodeSelect = function (params) {
            if (params.nodes[0] && params.nodes[0] != 0) {
                var n = $scope.nodes.get(params.nodes[0]);
                window.open(n.title, '_newtab');
            }
        };

        $scope.search = function () {
            if ($scope.query === undefined) {
                return;
            }
            if($scope.searchingInProcess){
                window.location = '/';
            }
            iterateSearch(3);

            $scope.loadingText = "Loading ...";
            $scope.loadingSubText = "This could take a while";
            $scope.searchingDone = false;
            $scope.isSearching = true;
            $scope.searchingInProcess = true;
            $scope.searchFailed = false;
            $scope.resetGraph();

            $scope.canceler = $q.defer();

            searchService.search($scope.query, $scope.sliders.nbPages.sliderValue,
                $scope.sliders.confidence.sliderValue,
                $scope.sliders.similarity.sliderValue, $scope.canceler)
                .then(function (result) {
                    $scope.searchingInProcess = false;
                    $scope.searchingDone = true;
                    $scope.results.length = 0;
                    $scope.results = result;
                    createGraph();

                }, function () {
                    $scope.searchFailed = true;
                    $scope.searchingInProcess = false;
                })
        };

        $scope.cancelRequest = function () {
            $scope.canceler.resolve();
        };

        function createGraph() {
            var index = 1000;
            $scope.nodes.add([
                {id: 0, label: $scope.query, group: 0, value: 10}
            ]);
            var nodes = [];
            var edges = [];
            for (var i = 0; i < $scope.results.length; i++) {
                index++;
                if ($scope.results[i].mainConcept === undefined) {
                    $scope.nodes.add({label: 'unknown', id: index, group: index});
                } else {
                    $scope.nodes.add({
                        label: $scope.results[i].mainConcept.name || '',
                        id: index,
                        title: $scope.results[i].mainConcept.uri,
                        group: index
                    });
                }
                $scope.edges.add({from: 0, to: index});

                for (var j = 0; j < $scope.results[i].graph.nodes.length; j++) {
                    $scope.results[i].graph.nodes[j].group = index;
                    nodes.push($scope.results[i].graph.nodes[j]);
                    $scope.edges.add({from: index, to: $scope.results[i].graph.nodes[j].id});
                }
                for (var j = 0; j < $scope.results[i].graph.edges.length; j++) {
                    edges.push($scope.results[i].graph.edges[j]);
                }
            }
            console.log(nodes);
            console.log(edges);
            $scope.nodes.add(nodes);
            $scope.edges.add(edges);

            $scope.nodes.add({id: 500, label: "new"});
            $scope.edges.add({from: 0, to: 500});
            $scope.nodes.remove({id: 500, label: "new"});

        }

        $scope.redraw = function (time) {
            var timeout = time || 500;
            $timeout(function () {
                var options = {
                    offset: {x: 0, y: 0},
                    duration: 1000,
                    easingFunction: "easeInOutQuad"
                };
                $scope.network.fit({animation: options});
            }, timeout);
        };

    }]);


