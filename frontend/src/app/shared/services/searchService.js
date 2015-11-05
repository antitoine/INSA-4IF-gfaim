gfaimApp.service('searchService', ['Restangular', '$log', '$q', search]);

function search(Restangular, $log, $q) {
    var self = this;
    self.search = function (query, confidence, similarity, canceler) {
        return $q(function (resolve, reject) {
            Restangular.one('/search?q='+query+'&confidence='+confidence+'&similarity'+similarity)
                .withHttpConfig({timeout: canceler.promise})
                .get()
                .then(function (data) {
                    if(data.plain){
                        resolve(data.plain());
                    } else {
                        reject('Erreur de connexion !');
                    }
                }, function () {
                    reject('Erreur de connexion !');
                })
        })
    };

}