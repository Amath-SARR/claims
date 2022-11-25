(function ($) {
    angular.module('Claims').factory('Intervenant', ['$http', ($http) => {
        let routePrefix = 'intervenant';
        return {
            index: function () {
                return $http.get(`/${routePrefix}`);
            },
            store: function (intervenant) {
                return $http.post(`/${routePrefix}`, intervenant);
            },
            update: function (intervenant) {
                return $http.put(`/${routePrefix}/${intervenant.id}`, intervenant);
            },
            destroy: function (intervenant) {
                return $http.delete(`/${routePrefix}/${intervenant.id}`, intervenant);
            },
            show: function (id) {
                return $http.get(`/${routePrefix}/${id}`);
            },
        };
    }]);
}(jQuery));
