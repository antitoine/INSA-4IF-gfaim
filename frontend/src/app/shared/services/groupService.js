gfaimApp.service('searchService', ['Restangular', '$log', '$q', search]);

function search(Restangular, $log, $q) {
  var self = this;

  self.search = function (text) {
    return $q(function (resolve, reject) {
      Restangular.one('/classe/' + text + '/').get
        .then(function () {
          resolve(data.plain);
        }, function () {
          reject('Erreur de connexion !');
        })
    })
  };

}