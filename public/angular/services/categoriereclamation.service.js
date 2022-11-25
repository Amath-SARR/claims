(function($) {
    angular.module('Claims').factory('CategorieReclamation', ['$http', ($http) => {
        let routePrefix = 'categoriereclamation';
        return {
            store: function(categorieReclamation) {
                return $http.post(`/${routePrefix}`, categorieReclamation);
            },
            update: function(categorieReclamation) {
                return $http.put(`/${routePrefix}/${categorieReclamation.id}`, categorieReclamation);
            },
            destroy: function(categorieReclamation) {
                return $http.delete(`/${routePrefix}/${categorieReclamation.id}`, categorieReclamation);
            },
            show: function(id) {
                return $http.get(`/${routePrefix}/${id}`);
            },
            getAuthUserCategoriesReclamation: function() {
                return $http.get(`/${routePrefix}/categories-reclamation`);
            },
        };
    }]);
}(jQuery));