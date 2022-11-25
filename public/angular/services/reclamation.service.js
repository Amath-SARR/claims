(function($) {
    angular.module('Claims').factory('Reclamation', ['$http', ($http) => {
        let routePrefix = 'reclamation';
        return {
            index: function() {
                return $http.get(`/${routePrefix}`);
            },
            dashboard: function(params) {
                return $http.post(`/${routePrefix}/dashboard`, params);
            },
            store: function(reclamation) {
                return $http.post(`/${routePrefix}`, reclamation);
            },
            update: function(reclamation) {
                return $http.put(`/${routePrefix}/${reclamation.id}`, reclamation);
            },
            archive: function(reclamation) {
                return $http.put(`/${routePrefix}/archive/${reclamation.id}`, reclamation);
            },
            moveToState: function(reclamation) {
                return $http.put(`/${routePrefix}/move/${reclamation.id}`, reclamation);
            },
            destroy: function(reclamation) {
                return $http.delete(`/${routePrefix}/${reclamation.id}`, reclamation);
            },
            show: function(id) {
                return $http.get(`/${routePrefix}/${id}`);
            },
            getSatisfactionReclamation: function(uid) {
                return $http.get(`/${routePrefix}/satisfaction/${uid}`);
            },
            getInsatisfactionReclamation: function(uid) {
                return $http.get(`/${routePrefix}/insatisfaction/${uid}`);
            },
            paginate: function(selectedPageSizeOption, pageNumber = null,) {
                if (pageNumber) {
                    return $http.post(`/${routePrefix}/paginate?page=${pageNumber}`,{selectedPageSizeOption});
                }
                return $http.post(`/${routePrefix}/paginate`,selectedPageSizeOption);
            },
        };
    }]);
}(jQuery));
