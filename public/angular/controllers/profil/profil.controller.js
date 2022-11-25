(function($) {
    angular.module('Claims').controller('ProfilController', function($scope, Profil) {
        $scope.profils = [];
        $scope.selectedProfil = null;
        $scope.newProfil = {};
        $scope.creationErrors = {};
        $scope.editionErrors = {};
        var profilEditModal = new bootstrap.Modal($('#profilEditModal'), {
            keyboard: false
        });

        $scope.index = () => {
            Profil.index()
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.profils = response.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };
        $scope.index();

        $scope.store = () => {
            $scope.creationErrors = {};
            Profil.store($scope.newProfil)
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
                        $scope.profils.unshift(response.data);
                        toastr.success('Profil créé avec succès...')
                        $scope.newProfil = {};
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.update = () => {
            $scope.editionErrors = {};
            Profil.update($scope.selectedProfil)
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
                        profilEditModal.hide();
                        $scope.index();
                        $scope.selectedProfil = {};
                        toastr.success("Profil mis à jour !");
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.destroy = (profil) => {
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
                    Profil.destroy(profil)
                        .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                $scope.index();
                                toastr.success('Profil supprimé avec succès...');
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        })
                        .finally(() => {
                            swal.close();
                        });
                });
        };

        $scope.openEditModal = (profil) => {
            $scope.selectedProfil = angular.copy(profil);
            profilEditModal.show();
        }

    });

}(jQuery));
