gfaimApp.service('searchService', ['Restangular', '$log', '$q', search]);

function search(Restangular, $log, $q) {
    var self = this;
    self.search = function (query) {
        return $q(function (resolve, reject) {
            Restangular.one('/search?q='+query)
                .get()
                .then(function (data) {
                    if(data.plain()){
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