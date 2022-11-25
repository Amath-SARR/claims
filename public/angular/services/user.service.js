(function ($) {
    angular.module('Claims').factory('User', ['$http', ($http) => {
        let routePrefix = 'user';
        return {
            index: function () {
                return $http.get(`/${routePrefix}`);
            },
            store: function (user) {
                return $http.post(`/${routePrefix}`, user);
            },
            update: function (user) {
                return $http.put(`/${routePrefix}/${user.id}`, user);
            },
            destroy: function (user) {
                return $http.delete(`/${routePrefix}/${user.id}`, user);
            },
            show: function (id) {
                return $http.get(`/${routePrefix}/${id}`);
            },
            attributeNewPassword: function (user, newPassword) {
                return $http.put(`/${routePrefix}/attribute-new-password/${user.id}`, { newPassword: newPassword });
            },
            updatePhoto: function (user) {
                return $http.put(`/${routePrefix}/update-photo/${user.id}`, user);
            },
            getUsers: function () {
                return $http.get(`/${routePrefix}/reclamations`);
            },
            getResponsablesReclamation: function (reclamationId) {
                return $http.get(`/${routePrefix}/responsables-reclamation/${reclamationId}`);
            }
        };
    }]);
}(jQuery));
