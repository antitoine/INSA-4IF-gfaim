gfaimApp.controller('welcomeCtrl', ['$scope', 'searchService', '$log', '$state',
    function ($scope, searchService, $log, $state) {

        $scope.isSearching = false;
        $scope.results=[];

        $scope.search = function () {
            $scope.isSearching = true;
            $scope.resetGraph();
            $scope.nodes.add([
                {id: 0, label: $scope.query, group: 0}
            ]);
            //searchService.search($scope.query)
            //    .then(function(result){
            //        $scope.results.length = 0;
            //        $scope.results = result;
            //    })
        };

        $scope.externalLinks = [
            {
                title: "titre du résultat",
                url: "http;//google.fr",
                description: "ceci est une longue description ceci est une longue description"
            },
            {
                title: "titre du résultat",
                url: "http;//google.fr",
                description: "ceci est une longue description ceci est une longue description"
            },
            {
                title: "titre du résultat",
                url: "http;//google.fr",
                description: "ceci est une longue description ceci est une longue description"
            }
        ];



        $scope.nodes = new vis.DataSet();
        $scope.edges = new vis.DataSet();

        var index = 5;

        var isConceptsShowing = false;

        var conceptsInfos = {
            1 : {isShowing: false},
            2 : {isShowing: false},
            3 : {isShowing: false},
            4 : {isShowing: false}
        };

        $scope.concepts = [
            {id: 1, label: "chocolat", group: 2},
            {id: 2, label: "fraise", group: 2},
            {id: 3, label: "carotte", group : 2},
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


        $scope.resetGraph = function(){
            $scope.nodes.clear();
            $scope.edges.clear();
        };

        $scope.onNodeSelect = function(params) {

            if(params.nodes[0]){
                var selected = params.nodes[0];
                console.log(params);
                if(selected == 0 && !isConceptsShowing) {
                    $scope.addConcepts();
                    isConceptsShowing = true;
                }
                if(selected != 0 && !conceptsInfos[selected].isShowing){
                    clickConcept(selected);
                }
            }
            var options = {offset: {x:0,y:0},
                duration: 2000,
                easingFunction: "easeInOutQuad"
            };
            $scope.network.fit({animation:options});
        };

        var clickConcept = function(groupid){
            for(var i = 0; i<5; i++){
                $scope.nodes.add({id : index, group: groupid});
                $scope.edges.add({from: index, to: groupid});
                index++;
            }
            conceptsInfos[groupid].isShowing = true;
        };

        $scope.addConcepts = function(){
            $scope.nodes.add($scope.concepts);
            for(var i = 0; i < $scope.concepts.length; i++){
                $scope.edges.add(
                    {from: 0, to : $scope.concepts[i].id}
                );
            }
        };

    }]);


