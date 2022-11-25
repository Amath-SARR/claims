(function($) {
    angular.module('Claims').factory('Priorite', ['$http', ($http) => {
        let routePrefix = 'priorite';
        return {
            index: function() {
                return $http.get(`/${routePrefix}`);
            },
            store: function(priorite) {
                return $http.post(`/${routePrefix}`, priorite);
            },
            update: function(priorite) {
                return $http.put(`/${routePrefix}/${priorite.id}`, priorite);
            },
            destroy: function(priorite) {
                return $http.delete(`/${routePrefix}/${priorite.id}`, priorite);
            },
            show: function(id) {
                return $http.get(`/${routePrefix}/${id}`);
            }
        };
    }]);
}(jQuery));
