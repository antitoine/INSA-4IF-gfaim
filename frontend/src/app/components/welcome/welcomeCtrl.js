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

  //      var index = 5;

        var isConceptsShowing = false;

        $scope.network_data = {
            nodes: $scope.nodes,
            edges: $scope.edges
        };

        var container = document.getElementById('container-network');
        $scope.network = new vis.Network(container, $scope.network_data, $scope.network_options || {});

        $scope.network_options = {
            "edges": {
                "smooth": false
            },
            "physics": {
                "enabled": false,
                "minVelocity": 0.75
            }
        };


        $scope.resetGraph = function () {
            $scope.nodes.clear();
            $scope.edges.clear();
            isConceptsShowing = false;
            //for (var index in conceptsInfos) {
            //    //conceptsInfos[index].isShowing = false;
            //}
        };

/*        $scope.onNodeSelect = function (params) {

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
        };*/

/*        var clickConcept = function (groupid) {
            for (var i = 0; i < 5; i++) {
                $scope.nodes.add({id: index, group: groupid});
                $scope.edges.add({from: index, to: groupid});
                index++;
            }
            conceptsInfos[groupid].isShowing = true;
        };*/

/*        $scope.addConcepts = function () {
            $scope.nodes.add($scope.concepts);
            for (var i = 0; i < $scope.concepts.length; i++) {
                $scope.edges.add(
                    {from: 0, to: $scope.concepts[i].id}
                );
            }
        };*/


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
            $scope.searchFailed = false;
            $scope.resetGraph();

            $scope.canceler = $q.defer();

            searchService.search($scope.query, $scope.sliders.confidence.sliderValue,
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

        $scope.cancelRequest = function(){
            $scope.canceler.resolve();
        };

        function createGraph(){
            var index = 1000;
            $scope.nodes.add([
                {id: 0, label: $scope.query, group: 0, value : 10}
            ]);
            var nodes = [];
            var edges = [];
            for(var i = 0; i < $scope.results.length; i ++){
                index++;
                if($scope.results[i].mainConcept === undefined){
                    $scope.nodes.add({label : 'no main concept', id : index, group : index});
                }else {
                    $scope.nodes.add({label: $scope.results[i].mainConcept.name || '', id : index, title:$scope.results[i].mainConcept.uri,
                        group : index});
                }
                $scope.edges.add({from: 0, to : index});

                for(var j = 0; j < $scope.results[i].graph.nodes.length; j++){
                    $scope.results[i].graph.nodes[j].group = index;
                    nodes.push($scope.results[i].graph.nodes[j]);
                    $scope.edges.add({from: $scope.results[i].graph.nodes[j].id, to : index});
                }
                for(var j = 0; j < $scope.results[i].graph.edges.length; j++){
                    edges.push($scope.results[i].graph.edges[j]);
                }
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


  /*      $scope.results =
            [{"graph":{"nodes":[{"id":7,"title":"http:\/\/www.food.com\/recipe\/roasted-tomato-soup-131639","label":"roasted-tomato-soup-131639"},{"id":2,"title":"http:\/\/www.foodnetwork.com\/recipes\/ree-drummond\/best-tomato-soup-ever.html","label":"best-tomato-soup-ever.html"},{"id":3,"title":"http:\/\/www.marthastewart.com\/315123\/tomato-soup","label":"tomato-soup"},{"id":4,"title":"http:\/\/www.vegrecipesofindia.com\/tomato-soup-recipe-restaurant-style\/","label":"http:\/\/www.vegrecipesofindia.com\/tomato-soup-recipe-restaurant-style\/"}],"edges":[{"from":7,"to":2,"title":1.5689045936396},{"from":7,"to":3,"title":2.2741116751269},{"from":7,"to":4,"title":1.6077738515901}]},"mainConcept":{"name":"Toobs","description":"The tomato is the edible, often red fruit\/berry of the nightshade Solanum lycopersicum, commonly known as a tomato plant. The species originated in the South American Andes and its use as a food originated in Mexico, and spread throughout the world following the Spanish colonization of the Americas.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Toobs","image":"http:\/\/en.wikipedia.org\/wiki\/Special:FilePath\/Bright_red_tomato_and_cross_section02.jpg?width=300","imageCaption":"Cross-section and full view of a hothouse  tomato","uri":"http:\/\/dbpedia.org\/resource\/Tomato","recipies":[]},"tags":["Tomato","Onion","Potato","Mung bean","Spinach","Garlic","Milk","Cherry tomato","Butter","Chicken (food)"],"externLinks":[{"url":"http:\/\/www.food.com\/recipe\/roasted-tomato-soup-131639","title":"Roasted Tomato Soup Recipe - Food.com","description":"Recently had an extra pint of cherry tomatoes on hand and was in search of \ncomfort food. Devised this soup in a pinch and thought it was worth sharing."},{"url":"http:\/\/www.foodnetwork.com\/recipes\/ree-drummond\/best-tomato-soup-ever.html","title":"Best Tomato Soup Ever Recipe : Ree Drummond : Food Network","description":"Get this all-star, easy-to-follow Best Tomato Soup Ever recipe from Ree \nDrummond."},{"url":"http:\/\/www.marthastewart.com\/315123\/tomato-soup","title":"Tomato Soup Recipe | Martha Stewart","description":"Tomato Soup. This simple recipe for delicious tomato soup is part of the Kirkland \nSignature Martha Stewart product line, available at Costco. Yield: Makes 6 cups."},{"url":"http:\/\/www.vegrecipesofindia.com\/tomato-soup-recipe-restaurant-style\/","title":"tomato soup recipe, how to make tomato soup | restaurant style","description":"Mar 18, 2015 ... tomato soup recipe with step by step photos. indian restaurant style tomato soup \nrecipe. tomato soup is popular in all indian restaurants."}]},{"graph":{"nodes":[{"id":6,"title":"http:\/\/www.finecooking.com\/recipes\/meyers-classic-tomato-soup.aspx","label":"meyers-classic-tomato-soup.aspx"}],"edges":[]},"mainConcept":{"name":"Deuk Deuk Tong","description":"Sesame (\/\u02c8s\u025bs\u0259mi\u02d0\/; Sesamum indicum) is a flowering plant in the genus Sesamum. Numerous wild relatives occur in Africa and a smaller number in India. It is widely naturalized in tropical regions around the world and is cultivated for its edible seeds, which grow in pods.Sesame seed is one of the oldest oilseed crops known, domesticated well over 3000 years ago.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Deuk_Deuk_Tong","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Sesamum_indicum_-_K\u00f6hler\u2013s_Medizinal-Pflanzen-129.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Sesame","recipies":[]},"tags":["Sesame","Parmigiano-Reggiano","Basil","Artichoke","Onion","Chives","Dill","Mozzarella","Yogurt","Garlic"],"externLinks":[{"url":"http:\/\/www.finecooking.com\/recipes\/meyers-classic-tomato-soup.aspx","title":"Classic Tomato Soup","description":"Nov 5, 2014 ... Silky tomato soup is like the little black dress of soups. Unadorned and paired \nwith a grilled cheese sandwich, it's a comforting lunch. Dressed\u00a0..."}]},{"graph":{"nodes":[{"id":5,"title":"http:\/\/www.eatingwell.com\/recipes\/tomato_soup.html","label":"tomato soup.html"}],"edges":[]},"mainConcept":{"name":"Snow skin mooncake","description":"Sugar is the generalized name for sweet, short-chain, soluble carbohydrates, many of which are used in food. They are carbohydrates, composed of carbon, hydrogen, and oxygen. There are various types of sugar derived from different sources. Simple sugars are called monosaccharides and include glucose (also known as dextrose), fructose and galactose. The table or granulated sugar most customarily used as food is sucrose, a disaccharide.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Snow_skin_mooncake","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Raw_sugar_closeup.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Sugar","recipies":[]},"tags":["Sugar","Pasta","Blue cheese","Baking sheet","Halloween","Casserole","Shelburne, Ontario","Blender","Popeye","United States"],"externLinks":[{"url":"http:\/\/www.eatingwell.com\/recipes\/tomato_soup.html","title":"Tomato Soup Recipe - EatingWell","description":"This simple tomato soup is perfect paired with your favorite grilled cheese \nsandwich. Make a double batch and freeze the extra for rainy-day emergenci."}]},{"graph":{"nodes":[{"id":4,"title":"http:\/\/www.cookinglight.com\/eating-smart\/smart-choices\/healthy-lunch-ideas\/fresh-tomato-soup-recipes","label":"fresh-tomato-soup-recipes"}],"edges":[]},"mainConcept":{"name":"Quinoa","description":"Quinoa (\/\u02c8ki\u02d0nw\u0251\u02d0\/,  from Quechua: kinwa), is a species of the goosefoot genus (Chenopodium quinoa), a grain crop grown primarily for its edible seeds. It is a pseudocereal rather than a true cereal, as it is not a member of the true grass family. As a chenopod, quinoa is closely related to species such as beetroots, spinach and tumbleweeds.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Quinoa","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Chenopodium_quinoa0.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Quinoa","recipies":[]},"tags":["Quinoa","Basil","Yogurt","Butter","Chenopodium leptophyllum","Chenopodium desiccatum","Chenopodium berlandieri","Chenopodium nutans","Chenopodium oahuense","Chenopodium hastatum"],"externLinks":[{"url":"http:\/\/www.cookinglight.com\/eating-smart\/smart-choices\/healthy-lunch-ideas\/fresh-tomato-soup-recipes","title":"Fresh Tomato Soup - Our Best Healthy Lunch Ideas - Cooking Light","description":"Nothing says comfort more than a warm bowl of tomato soup. Ditch the can and \nprepare this homemade soup with fresh veggies. Buying in season is smart on\u00a0..."}]},{"graph":{"nodes":[{"id":2,"title":"http:\/\/allrecipes.com\/recipes\/14731\/soups-stews-and-chili\/soup\/vegetable-soup\/tomato-soup\/","label":"http:\/\/allrecipes.com\/recipes\/14731\/soups-stews-and-chili\/soup\/vegetable-soup\/tomato-soup\/"}],"edges":[]},"mainConcept":{"name":"Celery Victor","description":"Bell pepper, also known as sweet pepper or a pepper (in the United Kingdom, Canada and Ireland) and capsicum \/\u02c8k\u00e6ps\u0268k\u0259m\/ (in India, Pakistan, Bangladesh, Australia, Singapore and New Zealand), is a cultivar group of the species Capsicum annuum. Cultivars of the plant produce fruits in different colors, including red, yellow, orange, green, chocolate\/brown, vanilla\/white, and purple.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Celery_Victor","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Poivrons_Luc_Viatour.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Bell_pepper","recipies":[]},"tags":["Bell pepper","Onion","Apple","Basil","Coriander","Spinach","Cream cheese","Parsley","Garlic","Mozzarella"],"externLinks":[{"url":"http:\/\/allrecipes.com\/recipes\/14731\/soups-stews-and-chili\/soup\/vegetable-soup\/tomato-soup\/","title":"Tomato Soup Recipes - Allrecipes.com","description":"Looking for tomato soup recipes? Allrecipes has more than 70 trusted tomato \nrecipes complete with ratings, reviews and cooking tips."}]},{"graph":{"nodes":[{"id":3,"title":"http:\/\/www.chowhound.com\/recipes\/creamy-tomato-soup-10836","label":"creamy-tomato-soup-10836"}],"edges":[]},"mainConcept":{"name":"Coppia Ferrarese","description":"Olive oil is a fat obtained from the olive (the fruit of Olea europaea; family Oleaceae), a traditional tree crop of the Mediterranean Basin.The oil is produced by pressing whole olives.It is commonly used in cooking, cosmetics, pharmaceuticals, and soaps and as a fuel for traditional oil lamps. Olive oil is used throughout the world and is especially associated with Mediterranean countries.","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Coppia_Ferrarese","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Italian_olive_oil_2007.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Olive_oil","recipies":[]},"tags":["Olive oil","Basil","Lemon","Chickpea","Falafel","Shallot","Garlic","Onion","Butter","Chicken (food)"],"externLinks":[{"url":"http:\/\/www.chowhound.com\/recipes\/creamy-tomato-soup-10836","title":"Classic Tomato Soup Recipe - Chowhound","description":"This tomato soup recipe is a comfort food that's almost as easy to make as the \ncondensed variety but is much, much tastier."}]},{"graph":{"nodes":[{"id":1,"title":"http:\/\/allrecipes.com\/recipe\/39544\/garden-fresh-tomato-soup\/","label":"http:\/\/allrecipes.com\/recipe\/39544\/garden-fresh-tomato-soup\/"}],"edges":[]},"mainConcept":{"name":"Bigoli in salsa","description":"","wikipediaUrl":"http:\/\/en.wikipedia.org\/wiki\/Bigoli_in_salsa","image":"http:\/\/commons.wikimedia.org\/wiki\/Special:FilePath\/Mixed_onions.jpg?width=300","imageCaption":"","uri":"http:\/\/dbpedia.org\/resource\/Onion","recipies":[]},"tags":["Onion","Basil","Garlic","Lime (fruit)","Milk","Butter","Chicken (food)","Clove","Syzygium luehmannii","Syzygium anisatum"],"externLinks":[{"url":"http:\/\/allrecipes.com\/recipe\/39544\/garden-fresh-tomato-soup\/","title":"Garden Fresh Tomato Soup Recipe - Allrecipes.com","description":"A simple, homemade soup made with fresh tomatoes is a perfect summertime \ntreat when the best tomatoes are ripe in gardens and farmers' markets. Everyone\n\u00a0..."}]}]
*/

    }]);


