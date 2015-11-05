gfaimApp.controller('welcomeCtrl', ['$scope', 'searchService', '$log', '$state', '$timeout', '$q',
    function ($scope, searchService, $log, $state, $timeout, $q) {

        $scope.isSearching = false;
        $scope.searchingInProcess = false;
        $scope.searchingDone = false;
        $scope.results = [];
        $scope.isCollapsed = true;
        $scope.loadingText = "Loading ...";
        $scope.loadingSubText= "This could take a while";

        $scope.sliders = {
            similarity : {
                sliderValue : 1.5,
                min : 0,
                step :0.5,
                max: 5,
                value : 1.5
            },
            confidence : {
                sliderValue : 1,
                min : 0,
                step :0.2,
                max: 2,
                value : 1
            }
        };

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
            {id: 1, title: "chocolat", group: 2},
            {id: 2, title: "fraise", group: 2},
            {id: 3, title: "carotte", group: 2},
            {id: 4, title: "recette", group: 2}
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
            },
            "edges": {
                "smooth": {
                    "forceDirection": "none"
                }
            },
            "physics": {
                "forceAtlas2Based": {
                    "gravitationalConstant": -47,
                    "springLength": 220,
                    "avoidOverlap": 1
                },
                "minVelocity": 0.75,
                "solver": "forceAtlas2Based"
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
            if($scope.query === undefined){
                return;
            }
            iterateSearch(3);
            $scope.loadingText = "Loading ...";
            $scope.loadingSubText= "This could take a while";
            $scope.searchingDone = false;
            $scope.isSearching = true;
            $scope.searchingInProcess = true;
            $scope.resetGraph();
            createGraph();

            $scope.nodes.add([
                {id: 0, label: $scope.query, group: 0}
            ]);
            $scope.canceler = $q.defer();

            searchService.search($scope.query, $scope.sliders.confidence.sliderValue,
                $scope.sliders.similarity.sliderValue, $scope.canceler)
                .then(function (result) {
                    $scope.searchingInProcess = false;
                    $scope.searchingDone = true;
                    $scope.results.length = 0;
                    $scope.results = result;
                }, function () {
                    $scope.searchFailed = true;
                    $scope.searchingInProcess = false;
                })
        };

        $scope.cancelRequest = function(){
            $scope.canceler.resolve();
        };

        function createGraph(){
            var nodes = [];
            var edges = [];
            var incr = 0;
            for(var i = 0; i < $scope.results.length; i ++){
                for(var j = 0; j < $scope.results[i].graph.nodes.length; j++){
                    $scope.results[i].graph.nodes[j].id += incr;
                    nodes.push($scope.results[i].graph.nodes[j]);
                }
                for(var j = 0; j < $scope.results[i].graph.edges.length; j++){
                    $scope.results[i].graph.edges[j].from += incr;
                    $scope.results[i].graph.edges[j].to += incr;
                    edges.push($scope.results[i].graph.edges[j]);
                }
                incr = incr + $scope.results[i].graph.nodes.length;
            }
            console.log(nodes);
            console.log(edges);
            $scope.nodes.add(nodes);
            $scope.edges.add(edges);

        }

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
        };


        //$scope.results =
        //    [{"graph":{"nodes":[{"id":7,"title":"http:\/\/www.southernliving.com\/food\/holidays-occasions\/fresh-tomato-recipes","label":"fresh-tomato-recipes"}],"edges":[]},"mainConcept":{"name":"Tomato","description":"The tomato is the edible, often red fruit\/berry of the nightshade Solanum lycopersicum, commonly known as a tomato plant. The species originated in the South American Andes and its use as a food originated in Mexico, and spread throughout the world following the Spanish colonization of the Americas.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Tomato","image":"http:\/\/en.wikipedia.org\/wiki\/Special:FilePath\/Bright_red_tomato_and_cross_section02.jpg?width=300","imageCaption":"Cross-section and full view of a hothouse  tomato","uri":"http:\/\/dbpedia.org\/resource\/Tomato"},"externLinks":[{"url":"http:\/\/www.southernliving.com\/food\/holidays-occasions\/fresh-tomato-recipes","title":"Fresh Tomato Recipes - Southern Living","description":"Incorporate fresh tomatoes into your everyday meals this summer with these \ngrilled tomato recipes, tomato pasta recipes, fried green tomatoes recipes, pico \nde\u00a0..."}]},{"graph":{"nodes":[{"id":8,"title":"http:\/\/www.sunset.com\/food-wine\/kitchen-assistant\/easy-fresh-tomato-recipes","label":"easy-fresh-tomato-recipes"}],"edges":[]},"mainConcept":{"name":"Salsa (sauce)","description":"Salsa is the Spanish term for sauce, and in English-speaking countries usually refers to the sauces typical of Mexican cuisine known as salsa picante, particularly those used as dips. They are often tomato-based, although many are not, and they are typically piquant, ranging from mild to extremely hot.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Salsa_(sauce)","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Mexico.Salsa.02.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Salsa_(sauce)"},"externLinks":[{"url":"http:\/\/www.sunset.com\/food-wine\/kitchen-assistant\/easy-fresh-tomato-recipes","title":"Great Fresh Tomato Recipes - Sunset","description":"Celebrate summer with these fresh tomato recipes. Try these soups, salads, \nsalsas, sandwiches, pastas, and more, all made with juicy summer tomatoes."}]},{"graph":{"nodes":[{"id":9,"title":"http:\/\/www.sunset.com\/food-wine\/kitchen-assistant\/easy-fresh-tomato-recipes\/green-grape-tomato-appetizer","label":"green-grape-tomato-appetizer"}],"edges":[]},"mainConcept":{"name":"Muscat (grape and wine)","description":"","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Muscat_(grape_and_wine)","image":"","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Muscat_(grape_and_wine)"},"externLinks":[{"url":"http:\/\/www.sunset.com\/food-wine\/kitchen-assistant\/easy-fresh-tomato-recipes\/green-grape-tomato-appetizer","title":"Green Grape heirloom tomatoes - Great Fresh Tomato Recipes ...","description":"Celebrate summer with these fresh tomato recipes. Try these soups, salads, \nsalsas, sandwiches, pastas, and more, all made with juicy summer tomatoes."}]},{"graph":{"nodes":[{"id":6,"title":"http:\/\/www.midwestliving.com\/food\/fruits-veggies\/40-fresh-tomato-recipes-youll-love\/","label":"http:\/\/www.midwestliving.com\/food\/fruits-veggies\/40-fresh-tomato-recipes-youll-love\/"}],"edges":[]},"mainConcept":{"name":"Onion","description":"","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Onion","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Mixed_onions.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Onion"},"externLinks":[{"url":"http:\/\/www.midwestliving.com\/food\/fruits-veggies\/40-fresh-tomato-recipes-youll-love\/","title":"40 Fresh Tomato Recipes You'll Love | Midwest Living","description":"Here are some of our favorite ways to use fresh tomatoes in appetizers, side \ndishes, main dishes, salads, pastas and pizzas."}]},{"graph":{"nodes":[{"id":5,"title":"http:\/\/www.marthastewart.com\/274270\/tomato-recipes","label":"tomato-recipes"}],"edges":[]},"mainConcept":{"name":"Ginger","description":"","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Ginger","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Koeh-146-no_text.jpg?width=300","imageCaption":"1896","uri":"http:\/\/dbpedia.org\/resource\/Ginger"},"externLinks":[{"url":"http:\/\/www.marthastewart.com\/274270\/tomato-recipes","title":"Tomato Recipes | Martha Stewart","description":"Because they're easy to grow, tomatoes are the most common backyard garden \ncrop. There are thousands of varieties, including beefsteak, plum, and\u00a0..."}]},{"graph":{"nodes":[{"id":2,"title":"http:\/\/www.cookinglight.com\/food\/in-season\/fresh-tomato-recipes","label":"fresh-tomato-recipes"}],"edges":[]},"mainConcept":{"name":"Quinoa","description":"Quinoa (\/\u02c8ki\u02d0nw\u0251\u02d0\/,  from Quechua: kinwa), is a species of the goosefoot genus (Chenopodium quinoa), a grain crop grown primarily for its edible seeds. It is a pseudocereal rather than a true cereal, as it is not a member of the true grass family. As a chenopod, quinoa is closely related to species such as beetroots, spinach and tumbleweeds.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Quinoa","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Chenopodium_quinoa0.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Quinoa"},"externLinks":[{"url":"http:\/\/www.cookinglight.com\/food\/in-season\/fresh-tomato-recipes","title":"Fresh Tomato Recipes - Cooking Light","description":"There's so much to love about these seasonal beauties. When summer is here, \nput down the cans and head to the farmer's market for fresh tomato flavor that\u00a0..."}]},{"graph":{"nodes":[{"id":3,"title":"http:\/\/www.foodnetwork.com\/topics\/tomato.html","label":"tomato.html"}],"edges":[]},"mainConcept":{"name":"Food Network Magazine","description":"Food Network Magazine is a monthly food entertainment magazine founded by Hearst Corporation and Scripps Networks Interactive based on the latter's popular television network Food Network. The magazine debuted in 2008, originally as two newsstand-only test issues to be followed by the first official issue in June 2009. As of 2010, it reaches 5 million readers with each issue with a 1.35 million circulation. It is now published 10 times a year.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Food_Network_Magazine","image":"","imageCaption":"June\/July 2009 Cover","uri":"http:\/\/dbpedia.org\/resource\/Food_Network_Magazine"},"externLinks":[{"url":"http:\/\/www.foodnetwork.com\/topics\/tomato.html","title":"Tomato Recipes : Food Network","description":"Results 1 - 10 of 7073 ... Find tomato recipes, videos, and ideas from Food Network."}]},{"graph":{"nodes":[{"id":4,"title":"http:\/\/www.health.com\/health\/gallery\/0,,20723744,00.html","label":"0,,20723744,00.html"}],"edges":[]},"mainConcept":{"name":"Bloody Mary (cocktail)","description":"A Bloody Mary is a popular cocktail containing vodka, tomato juice, and combinations of other spices and flavorings including Worcestershire sauce, Tabasco sauce, piri piri sauce, beef consomm\u00e9 or bouillon, horseradish, celery, olives, salt, black pepper, cayenne pepper, lemon juice, and celery salt. It has been called \"the world's most complex cocktail\".","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Bloody_Mary_(cocktail)","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Bloody_Mary.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Bloody_Mary_(cocktail)"},"externLinks":[{"url":"http:\/\/www.health.com\/health\/gallery\/0,,20723744,00.html","title":"22 Mouthwatering Tomato Recipes - Health.com","description":"Here are 20 great tomato recipes including Bloody Marys, salsa, caprese stacks, \nand more."}]},{"graph":{"nodes":[{"id":1,"title":"http:\/\/allrecipes.com\/recipes\/1095\/fruits-and-vegetables\/vegetables\/tomatoes\/","label":"http:\/\/allrecipes.com\/recipes\/1095\/fruits-and-vegetables\/vegetables\/tomatoes\/"}],"edges":[]},"mainConcept":{"name":"Mozzarella","description":"Mozzarella (English \/\u02ccm\u0252ts\u0259\u02c8r\u025bl\u0259\/; Italian: [mottsa\u02c8r\u025blla]) is a cheese, originally from southern Italy, traditionally made from Italian buffalo milk by the pasta filata method.Mozzarella received a Traditional Specialities Guaranteed certification from the European Union in 1998. This protection scheme requires that mozzarella sold in the European Union is produced according to a traditional recipe. The TSG certification does not specify the source of the milk, so any type of milk can be used.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Mozzarella","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Cheese_07_bg_042906.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Mozzarella"},"externLinks":[{"url":"http:\/\/allrecipes.com\/recipes\/1095\/fruits-and-vegetables\/vegetables\/tomatoes\/","title":"Tomato Recipes - Allrecipes.com","description":"Top tomato sauces, salads, soups, sides\u2014see all the 5-star recipes for tomatoes, \nthe most versatile veggie."}]}]

    }]);


