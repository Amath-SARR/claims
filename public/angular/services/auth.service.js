(function($) {
    angular.module('Claims').factory('Auth', ['$http', ($http) => {
        let routePrefix = 'auth';
        return {
            login: function(loginData) {
                return $http.post(`/${routePrefix}/login`, loginData);
            },
            logout: function() {
                return $http.post(`/${routePrefix}/logout`, null);
            },
            changePassword: function(newPassword, currentPassword, confirmPassword) {
                return $http.put(`/${routePrefix}/change-password`, { currentPassword: currentPassword, newPassword: newPassword, confirmPassword: confirmPassword });
            },
            getConnectedUser: function() {
                return $http.get(`/${routePrefix}/account`);
            },
            checkEmailAdressAndSendMailForResetPassword: function(emailAdress) {
                return $http.post(`/${routePrefix}/reset-password-account`, emailAdress);
            },
            checkTokenAndgetEmailFromToken: function(token) {
                return $http.post(`/${routePrefix}/check-token`, token);
            },
            resetPassword: function(resetPasswordData) {
                return $http.post(`/${routePrefix}/reset-password`, resetPasswordData);
            }
        };
    }]);
}(jQuery));
