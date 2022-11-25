(function($) {
    angular.module('Claims').controller('PasswordResetController', function(Auth, $scope, $routeParams, $location, $route) {
        $scope.passwordErrors = null;
        $scope.userEmail = "";
        $scope.newPassword = "";
        $scope.confirmPassword = "";


        $scope.checkToken = () => {
            Auth.checkTokenAndgetEmailFromToken($routeParams)
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                        $location.url("/login");
                    } else {
                        $scope.userEmail = response.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        }
        $scope.checkToken();


        $scope.resetPassword = () => {

            Auth.resetPassword({ email: $scope.userEmail, newPassword: $scope.newPassword })
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        if (response.validationError) {
                            $scope.passwordErrors = response.data;
                        } else {
                            toastr.error(response.data);
                        }
                    } else {
                        toastr.success(response.data);
                        $location.url("/login");
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        }
    });
}(jQuery));
