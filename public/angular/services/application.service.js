(function($) {
    angular.module('Claims').factory('Application', ['$http', ($http) => {
        let routePrefix = 'application';
        return {
            index: function() {
                return $http.get(`/${routePrefix}`);
            },
            store: function(application) {
                return $http.post(`/${routePrefix}`, application);
            },
            update: function(application) {
                return $http.put(`/${routePrefix}/${application.id}`, application);
            },
            destroy: function(application) {
                return $http.delete(`/${routePrefix}/${application.id}`, application);
            },
            show: function(id) {
                return $http.get(`/${routePrefix}/${id}`);
            },
            updateLogo: function(application) {
                return $http.put(`/${routePrefix}/update-logo/${application.id}`, application);
            },
            getAuthUserApplications: function() {
                return $http.get(`/${routePrefix}/applications`);
            },
        };
    }]);
}(jQuery));