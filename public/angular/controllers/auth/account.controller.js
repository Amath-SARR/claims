(function($) {
    angular.module('Claims').controller('AccountController',
        function($scope, $rootScope, Auth, User, Application, CategorieReclamation) {
            $scope.editionErrors = {};
            $scope.newPassword = "";
            $scope.currentPassword = "";
            $scope.confirmPassword = "";
            $scope.interventionsApplications = [];
            $scope.gestionnairesCategoriesReclamation = [];
            var passwordEditModal = new bootstrap.Modal($('#passwordEditModal'), {
                keyboard: false
            });

            $scope.editPhoto = () => {
                angular.element('#photoInputEdit').trigger('click');
            };


            $('#photoInputEdit').on('change', (evt) => {
                convertImageToBase64String(evt)
                    .then((data) => {
                        $rootScope.currentUser.photo = data;
                        $scope.updatePhoto();
                    })
                    .catch(() => {})
            });

            $scope.updatePhoto = () => {
                User.updatePhoto($rootScope.currentUser)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $rootScope.currentUser.photo_full_path = response.data.photo_full_path;
                            toastr.success("Photo mise à jour !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.openEditModal = () => {
                passwordEditModal.show();
            };

            $scope.changePassword = () => {
                $scope.editionErrors = {};
                Auth.changePassword($scope.newPassword, $scope.currentPassword, $scope.newPassword)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            if (response.validationError) {
                                $scope.editionErrors = response.data;
                                Object.values($scope.editionErrors).forEach((error) => {
                                    toastr.error(error)
                                });
                            } else {
                                toastr.error(response.data);
                            }
                        } else {
                            passwordEditModal.hide();
                            toastr.success("Votre mot de passe a été changé avec succès");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.getAuthUserApplications = () => {
                Application.getAuthUserApplications()
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.interventionApplications = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            $scope.getAuthUserApplications();

            $scope.getAuthUserCategoriesReclamation = () => {
                CategorieReclamation.getAuthUserCategoriesReclamation()
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.gestionnairesCategoriesReclamation = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            $scope.getAuthUserCategoriesReclamation();

        });
}(jQuery));
