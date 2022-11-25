(function($) {
    angular.module('Claims').controller('LoginController', function(Auth, $scope) {
        $scope.loginModel = null;
        $scope.loginErrors = null;
        $scope.emailAdress = "";
        $scope.emailAdressErrors = null;
        $scope.isCheckingEmailAdress = false;
        var checkEmailAdressModal = new bootstrap.Modal($('#checkEmailAdressModal'), {
            keyboard: false
        });

        $scope.login = () => {
            Auth.login($scope.loginModel)
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        if (response.validationError) {
                            $scope.loginErrors = response.data;
                            Object.values($scope.loginErrors).forEach((error) => {
                                toastr.error(error)
                            });
                        } else {
                            toastr.error(response.data);
                        }
                    } else {
                        toastr.success("Authentification reussie !");
                        window.location.href = '/';
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.checkEmailAdress = () => {
            $scope.isCheckingEmailAdress = true;
            Auth.checkEmailAdressAndSendMailForResetPassword({ email: $scope.emailAdress })
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        if (response.validationError) {
                            $scope.emailAdressErrors = response.data.email;
                        } else {
                            toastr.error(response.data);
                        }
                    } else {
                        $scope.emailAdressErrors = null;
                        $scope.emailAdress = "";
                        checkEmailAdressModal.hide();
                        toastr.success(response.data)
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                })
                .finally(() => { $scope.isCheckingEmailAdress = false; });
        }
    });
}(jQuery));
