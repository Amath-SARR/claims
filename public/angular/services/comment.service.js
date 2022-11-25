(function ($) {
    angular.module('Claims').factory('Comment', ['$http', ($http) => {
        let routePrefix = 'comment';
        return {
            index: function () {
                return $http.get(`/${routePrefix}`);
            },
            store: function (comment) {
                return $http.post(`/${routePrefix}`, comment);
            },
            update: function (comment) {
                return $http.put(`/${routePrefix}/${comment.id}`, comment);
            },
            destroy: function (comment) {
                return $http.delete(`/${routePrefix}/${comment.id}`, comment);
            },
            show: function (id) {
                return $http.get(`/${routePrefix}/${id}`);
            },
            getComment: function(uid) {
                return $http.get(`/${routePrefix}/reclameur/${uid}`);
            }
        };
    }]);
}(jQuery));
