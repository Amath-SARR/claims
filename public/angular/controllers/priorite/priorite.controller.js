(function($) {
    angular.module('Claims').controller('PrioriteController', function($scope, Priorite) {
        $scope.priorites = [];
        $scope.selectedPriorite = null;
        $scope.newPriorite = {};
        $scope.creationErrors = {};
        $scope.editionErrors = {};
        var prioriteEditModal = new bootstrap.Modal($('#prioriteEditModal'), {
            keyboard: false
        });

        $scope.index = () => {
            Priorite.index()
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.priorites = response.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };
        $scope.index();

        $scope.store = () => {
            $scope.creationErrors = {};
            Priorite.store($scope.newPriorite)
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
                        $scope.priorites.unshift(response.data);
                        toastr.success('Priorité créée avec succès...')
                        $scope.newPriorite = {};
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.update = () => {
            $scope.editionErrors = {};
            Priorite.update($scope.selectedPriorite)
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
                        prioriteEditModal.hide();
                        toastr.success('Priorité mise à jour avec succès...')
                        $scope.index();
                        $scope.selectedPriorite = {};
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.destroy = (priorite) => {
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
                    Priorite.destroy(priorite)
                        .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                $scope.index();
                                toastr.success('Prioritée supprimé avec succès...');
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        })
                        .finally(() => swal.close());
                });
        };

        $scope.openEditModal = (priorite) => {
            $scope.selectedPriorite = angular.copy(priorite);
            prioriteEditModal.show();
        }

    });

}(jQuery));
