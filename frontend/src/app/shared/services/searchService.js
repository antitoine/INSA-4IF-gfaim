gfaimApp.service('searchService', ['Restangular', '$log', '$q', search]);

function search(Restangular, $log, $q) {
    var self = this;
    self.search = function (query) {
        return $q(function (resolve, reject) {
            Restangular.one('/search')
                .get()
                .then(function (data) {
                    resolve(data.plain());
                }, function () {
                    reject('Erreur de connexion !');
                })
        })
    };

}