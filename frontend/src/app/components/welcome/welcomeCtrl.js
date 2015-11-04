gfaimApp.controller('welcomeCtrl', ['$scope', 'searchService', '$log', '$state', '$timeout',
    function ($scope, searchService, $log, $state, $timeout) {

        $scope.isSearching = false;
        $scope.searchingInProcess = false;
        $scope.searchingDone = false;
        $scope.results = [];

        $scope.loadingText = "Loading ...";
        $scope.loadingSubText= "This could take a while";

        $scope.res =
            [{
                "graph": {
                    "nodes": [{
                        "id": 1,
                        "label": "http://allrecipes.com/recipe/39544/garden-fresh-tomato-soup/"
                    }, {
                        "id": 1,
                        "label": "http://allrecipes.com/recipes/14731/soups-stews-and-chili/soup/vegetable-soup/tomato-soup/"
                    }, {"id": 1, "label": "http://www.chowhound.com/recipes/creamy-tomato-soup-10836"}, {
                        "id": 1,
                        "label": "http://www.finecooking.com/recipes/meyers-classic-tomato-soup.aspx"
                    }, {
                        "id": 1,
                        "label": "http://www.foodnetwork.com/recipes/ree-drummond/best-tomato-soup-ever.html"
                    }, {"id": 1, "label": "http://www.marthastewart.com/315123/tomato-soup"}, {
                        "id": 1,
                        "label": "http://www.vegrecipesofindia.com/tomato-soup-recipe-restaurant-style/"
                    }, {"id": 1, "label": "http://www.food.com/recipe/roasted-tomato-soup-131639"}],
                    "edges": [{"from": 0, "to": 1, "label": 0.31297709923664}, {
                        "from": 0,
                        "to": 2,
                        "label": 0.2803738317757
                    }, {"from": 0, "to": 3, "label": 0.35211267605634}, {
                        "from": 0,
                        "to": 4,
                        "label": 0.23170731707317
                    }, {"from": 0, "to": 5, "label": 0.42666666666667}, {
                        "from": 1,
                        "to": 3,
                        "label": 0.26315789473684
                    }, {"from": 1, "to": 5, "label": 0.26923076923077}, {"from": 1, "to": 6, "label": 0.2}, {
                        "from": 2,
                        "to": 3,
                        "label": 0.22377622377622
                    }, {"from": 4, "to": 5, "label": 0.2093023255814}, {"from": 5, "to": 7, "label": 0.27083333333333}]
                },
                "mainConcept": {
                    "name": "name",
                    "uri": "http://dbpedia.org/resource/Onion",
                    "image": "http://dbpedia.org/statics/dbpedia_logo.png",
                    "imageCaption": "image caption",
                    "description": "my description"
                },
                "externLinks": [{"url": "http://allrecipes.com/recipe/39544/garden-fresh-tomato-soup/"}, {"url": "http://allrecipes.com/recipes/14731/soups-stews-and-chili/soup/vegetable-soup/tomato-soup/"}, {"url": "http://www.chowhound.com/recipes/creamy-tomato-soup-10836"}, {"url": "http://www.finecooking.com/recipes/meyers-classic-tomato-soup.aspx"}, {"url": "http://www.foodnetwork.com/recipes/ree-drummond/best-tomato-soup-ever.html"}, {"url": "http://www.marthastewart.com/315123/tomato-soup"}, {"url": "http://www.vegrecipesofindia.com/tomato-soup-recipe-restaurant-style/"}, {"url": "http://www.food.com/recipe/roasted-tomato-soup-131639"}]
            }, {
                "graph": {
                    "nodes": [{"id": 1, "label": "http://www.eatingwell.com/recipes/tomato_soup.html"}],
                    "edges": []
                },
                "mainConcept": {
                    "name": "name",
                    "uri": "http://dbpedia.org/resource/Sugar",
                    "image": "http://dbpedia.org/statics/dbpedia_logo.png",
                    "imageCaption": "image caption",
                    "description": "my description"
                },
                "externLinks": [{"url": "http://www.eatingwell.com/recipes/tomato_soup.html"}]
            }, {
                "graph": {
                    "nodes": [{
                        "id": 1,
                        "label": "http://www.cookinglight.com/eating-smart/smart-choices/healthy-lunch-ideas/fresh-tomato-soup-recipes"
                    }], "edges": []
                },
                "mainConcept": {
                    "name": "name",
                    "uri": "http://dbpedia.org/resource/Quinoa",
                    "image": "http://dbpedia.org/statics/dbpedia_logo.png",
                    "imageCaption": "image caption",
                    "description": "my description"
                },
                "externLinks": [{"url": "http://www.cookinglight.com/eating-smart/smart-choices/healthy-lunch-ideas/fresh-tomato-soup-recipes"}]
            }];


        function iterateSearch(index) {
            var searchWords =
            {
                3 : "Still loading ???",
                2 : "This is boring.",
                1 : "At least, it takes less time than cooking pastas...",
                0 : "Please !!!"
            };
            var searchWords2 =
            {
                3 : "",
                2 : "",
                1 : "and a pizza",
                0 : ""
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

        var index = 5;

        var isConceptsShowing = false;

        var conceptsInfos = {
            1: {isShowing: false},
            2: {isShowing: false},
            3: {isShowing: false},
            4: {isShowing: false}
        };

        $scope.concepts = [
            {id: 1, label: "chocolat", group: 2},
            {id: 2, label: "fraise", group: 2},
            {id: 3, label: "carotte", group: 2},
            {id: 4, label: "recette", group: 2}
        ];

        $scope.network_data = {
            nodes: $scope.nodes,
            edges: $scope.edges
        };

        var container = document.getElementById('container-network');
        $scope.network = new vis.Network(container, $scope.network_data, $scope.network_options || {});

        $scope.network_options = {
            "interaction": {
                "hover": true
            }
        };


        $scope.resetGraph = function () {
            $scope.nodes.clear();
            $scope.edges.clear();
            isConceptsShowing = false;
            for (var index in conceptsInfos) {
                conceptsInfos[index].isShowing = false;
            }
        };

        $scope.onNodeSelect = function (params) {

            if (params.nodes[0]) {
                var selected = params.nodes[0];
                console.log(params);
                if (selected == 0 && !isConceptsShowing) {
                    $scope.addConcepts();
                    isConceptsShowing = true;
                }
                if (conceptsInfos[selected]) {
                    if (selected != 0 && !conceptsInfos[selected].isShowing) {
                        clickConcept(selected);
                    } else {
                        var connectedNodes = $scope.network.getConnectedNodes(selected);
                        for (var i = 0; i < connectedNodes.length; i++) {
                            if (connectedNodes !== 0) {
                                $scope.nodes.remove({id: connectedNodes[i]});
                            }
                            conceptsInfos[selected].isShowing = false;
                        }
                    }
                } else {
                    clickConcept(selected);
                }
            }
            $scope.redraw(2000);
        };

        var clickConcept = function (groupid) {
            for (var i = 0; i < 5; i++) {
                $scope.nodes.add({id: index, group: groupid});
                $scope.edges.add({from: index, to: groupid});
                index++;
            }
            conceptsInfos[groupid].isShowing = true;
        };

        $scope.addConcepts = function () {
            $scope.nodes.add($scope.concepts);
            for (var i = 0; i < $scope.concepts.length; i++) {
                $scope.edges.add(
                    {from: 0, to: $scope.concepts[i].id}
                );
            }
        };


        $scope.search = function () {
            iterateSearch(3);
            $scope.loadingText = "Loading ...";
            $scope.loadingSubText= "This could take a while";

            $scope.isSearching = true;
            $scope.searchingInProcess = true;
            $scope.resetGraph();
            $scope.nodes.add([
                {id: 0, label: $scope.query, group: 0}
            ]);
            searchService.search($scope.query)
                .then(function (result) {
                    $scope.searchingInProcess = false;
                    $scope.searchingDone = true;
                    $scope.results.length = 0;
                    $scope.results = result;
                })
        };

        $scope.redraw = function (time) {
            timeout = time || 500;
            $timeout(function () {
                var options = {
                    offset: {x: 0, y: 0},
                    duration: 1000,
                    easingFunction: "easeInOutQuad"
                };
                $scope.network.fit({animation: options});
            }, timeout);
        }


    }]);


