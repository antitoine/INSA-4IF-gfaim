gfaimApp.service('searchService', ['Restangular', '$log', '$q', search]);

function search(Restangular, $log, $q) {
    var self = this;

    self.search = function (query) {
        return $q(function (resolve, reject) {
            Restangular.all()
                .customPOST(
                {query: query},
                {param: ""})
                .then(function () {
                    resolve(data.plain);
                }, function () {
                    reject('Erreur de connexion !');
                })
        })
    };

}