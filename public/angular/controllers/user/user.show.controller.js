(function($) {
    angular.module('Claims').controller('UserShowController',
        function($scope, $routeParams, User, Application, Intervenant, Gestionnaire) {
            $scope.user = {};
            $scope.applications = [];
            $scope.newPassword = "";
            var passwordEditModal = new bootstrap.Modal($('#passwordEditModal'), {
                keyboard: false
            });
            $scope.isAttributingNewPassord = false;

            $scope.show = () => {
                User.show($routeParams.id)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.user = response.data;
                            $scope.findAllApplications();
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.show();

            $scope.findAllApplications = () => {
                if ($scope.applications.length == 0) {
                    Application.index()
                        .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                $scope.applications = response.data;
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        });
                }
            }

            // gestion des intervenants
            $scope.addIntervenant = (application) => {
                let intervenant = { user_id: $routeParams.id, application_id: application.id };
                $scope.intervenantCreationErrors = null;
                Intervenant.store(intervenant)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            if (response.validationError) {
                                $scope.intervenantCreationErrors = response.data;
                                Object.values($scope.intervenantCreationErrors).forEach((error) => {
                                    toastr.error(error)
                                });
                            } else {
                                toastr.error(response.data);
                            }
                        } else {
                            $scope.user.intervenants.unshift(response.data);
                            toastr.success("Succès !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.destroyIntervenant = (intervenant) => {
                swal({
                        title: "Êtes-vous sur ?",
                        text: "Vous ne pourrez pas restaurer la valeur...",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Oui, Supprimer",
                        cancelButtonClass: "btn-primary",
                        cancelButtonText: 'Annuler',
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function() {
                        Intervenant.destroy(intervenant)
                            .then((response) => {
                                response = response.data;
                                if (response.error) {
                                    toastr.error(response.data);
                                } else {
                                    $scope.show();
                                }
                                toastr.success('Succès...');
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };
            // implémenter le crud des gestionnaires
            $scope.addNewGestionnaire = (categorieReclamation) => {
                let gestionnaire = { user_id: $routeParams.id, categorie_reclamation_id: categorieReclamation.id };
                Gestionnaire.store(gestionnaire)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            if (response.validationError) {
                                Object.values(response.data).forEach((error) => {
                                    toastr.error(error)
                                });
                            } else {
                                toastr.error(response.data);
                            }
                        } else {
                            $scope.user.gestionnaires.unshift(response.data);
                            toastr.success("Succès !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.destroyGestionnaire = (gestionnaire) => {
                swal({
                        title: "Êtes-vous sur ?",
                        text: "Vous ne pourrez pas restaurer la valeur...",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Oui, Supprimer",
                        cancelButtonClass: "btn-primary",
                        cancelButtonText: 'Annuler',
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function() {
                        Gestionnaire.destroy(gestionnaire)
                            .then((response) => {
                                response = response.data;
                                if (response.error) {
                                    toastr.error(response.data);
                                } else {
                                    $scope.show();
                                    toastr.success('Catégorie réclamation supprimé avec succès...');
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };

            $scope.attributeNewPassword = (newPassword) => {
                $scope.isAttributingNewPassord = true;
                User.attributeNewPassword($scope.user, newPassword)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data.newPassword[0]);
                        } else {
                            $scope.newPassword = "";
                            toastr.success(response.data);
                            passwordEditModal.hide();
                            toastr.success("Succès !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    })
                    .finally(() => { $scope.isAttributingNewPassord = false; });
            };

            $scope.selectPhotoEdit = () => {
                angular.element('#photoInputEdit').trigger('click');
            };


            $('#photoInputEdit').on('change', (evt) => {
                convertImageToBase64String(evt)
                    .then((data) => {
                        $scope.user.photo = data;
                        $scope.updatePhoto();
                    })
                    .catch(() => {})
            });

            $scope.updatePhoto = () => {
                User.updatePhoto($scope.user)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.user.photo_full_path = response.data.photo_full_path;
                            toastr.success("Succès !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
        });

}(jQuery));
