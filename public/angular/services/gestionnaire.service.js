(function ($) {
    angular.module('Claims').factory('Gestionnaire', ['$http', ($http) => {
        let routePrefix = 'gestionnaire';
        return {
            index: function () {
                return $http.get(`/${routePrefix}`);
            },
            store: function (gestionnaire) {
                return $http.post(`/${routePrefix}`, gestionnaire);
            },
            update: function (gestionnaire) {
                return $http.put(`/${routePrefix}/${gestionnaire.id}`, gestionnaire);
            },
            destroy: function (gestionnaire) {
                return $http.delete(`/${routePrefix}/${gestionnaire.id}`, gestionnaire);
            },
            show: function (id) {
                return $http.get(`/${routePrefix}/${id}`);
            }
        };
    }]);
}(jQuery));
