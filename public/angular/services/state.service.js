(function($) {
    angular.module('Claims').factory('State', ['$http', ($http) => {
        let routePrefix = 'state';
        return {
            index: function () {
                return $http.get(`/${routePrefix}`);
            },
            store: function (state) {
                return $http.post(`/${routePrefix}`, state);
            },
            update: function (state) {
                return $http.put(`/${routePrefix}/${state.id}`, state);
            },
            destroy: function (state) {
                return $http.delete(`/${routePrefix}/${state.id}`, state);
            },
            show: function (id) {
                return $http.get(`/${routePrefix}/${id}`);
            }
        };
    }]);
}(jQuery));
