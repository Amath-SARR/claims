(function ($) {
    angular.module('Claims').factory('ApplicationProfil', ['$http', ($http) => {
        let routePrefix = 'applicationprofil';
        return {
            index: function () {
                return $http.get(`/${routePrefix}`);
            },
            store: function (applicationprofil) {
                return $http.post(`/${routePrefix}`, applicationprofil);
            },
            update: function (applicationprofil) {
                return $http.put(`/${routePrefix}/${applicationprofil.id}`, applicationprofil);
            },
            destroy: function (applicationprofil) {
                return $http.delete(`/${routePrefix}/${applicationprofil.id}`, applicationprofil);
            },
            show: function (id) {
                return $http.get(`/${routePrefix}/${id}`);
            }
        };
    }]);
}(jQuery));
