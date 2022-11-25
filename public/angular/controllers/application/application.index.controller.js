(function($) {
    angular.module('Claims').controller('ApplicationIndexController', function($scope, Application) {
        $scope.applications = [];
        $scope.selectedApplication = null;
        $scope.newApplication = {};
        $scope.creationErrors = {};
        $scope.editionErrors = {};
        var applicationEditModal = new bootstrap.Modal($('#applicationEditModal'), {
            keyboard: false
        });

        $scope.index = () => {
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
        };
        $scope.index();

        $scope.store = () => {
            $scope.creationErrors = {};
            Application.store($scope.newApplication)
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        if (response.validationError) {
                            $scope.creationErrors = response.data;
                            Object.values($scope.creationErrors).forEach((error) => {
                                toastr.error(error)
                            });
                        } else {
                            toastr.error(response.data);
                        }
                    } else {
                        toastr.success('Application créée avec succès...');
                        $scope.applications.unshift(response.data);
                        $scope.newApplication = {};
                        $('#logoInput').val(null);
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.update = () => {
            $scope.editionErrors = {};
            Application.update($scope.selectedApplication)
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
                        applicationEditModal.hide();
                        toastr.success("Mise à jour reussie !");
                        $scope.index();
                        $scope.selectedApplication = {};
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.destroy = (application) => {
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
                    Application.destroy(application)
                        .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                $scope.index();
                                toastr.success('Application supprimée avec succès...');
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        })
                        .finally(() => swal.close());
                });
        };

        $scope.openEditModal = (application) => {
            $scope.selectedApplication = angular.copy(application);
            applicationEditModal.show();
        };

        $('#logoInput').on('change', (evt) => {
            convertImageToBase64String(evt)
                .then((data) => {
                    $scope.newApplication.logo = data;
                })
                .catch(() => {});
        });

        $scope.selectLogoEdit = (application) => {
            $scope.selectedApplication = application;
            angular.element('#logoInputEdit').trigger('click');
        };


        $('#logoInputEdit').on('change', (evt) => {
            convertImageToBase64String(evt)
                .then((data) => {
                    $scope.selectedApplication.logo = data;
                    $scope.updateLogo($scope.selectedApplication);
                })
                .catch(() => {});
        });

        $scope.updateLogo = (selectedApplication) => {
            Application.updateLogo(selectedApplication)
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.selectedApplication.logo_full_path = response.data.logo_full_path;
                        toastr.success("Logo mis à jour !")
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };
    });

}(jQuery));
