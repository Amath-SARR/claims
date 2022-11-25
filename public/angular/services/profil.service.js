(function($) {
    angular.module('Claims').factory('Profil', ['$http', ($http) => {
        let routePrefix = 'profil';
        return {
            index: function () {
                return $http.get(`/${routePrefix}`);
            },
            store: function (profil) {
                return $http.post(`/${routePrefix}`, profil);
            },
            update: function (profil) {
                return $http.put(`/${routePrefix}/${profil.id}`, profil);
            },
            destroy: function (profil) {
                return $http.delete(`/${routePrefix}/${profil.id}`, profil);
            },
            show: function (id) {
                return $http.get(`/${routePrefix}/${id}`);
            }
        };
    }]);
}(jQuery));
