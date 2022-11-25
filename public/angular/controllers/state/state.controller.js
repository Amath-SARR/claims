(function($) {
    angular.module('Claims').controller('StateController', function($scope, State) {
        $scope.states = [];
        $scope.selectedState = null;
        $scope.newState = {};
        $scope.creationErrors = {};
        $scope.editionErrors = {};
        var stateEditModal = new bootstrap.Modal($('#stateEditModal'), {
            keyboard: false
        });

        $scope.index = () => {
            State.index()
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.states = response.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };
        $scope.index();

        $scope.store = () => {
            $scope.creationErrors = {};
            State.store($scope.newState)
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
                        $scope.states.unshift(response.data);
                        toastr.success('Statut créé avec succès...')
                        $scope.newState = {};
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.update = () => {
            $scope.editionErrors = {};
            State.update($scope.selectedState)
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
                        stateEditModal.hide();
                        $scope.index();
                        $scope.selectedState = {};
                        toastr.success("Succès !");
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.destroy = (state) => {
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
                    State.destroy(state)
                        .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                $scope.index();
                                toastr.success('State supprimé avec succès...');
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        })
                        .finally(() => swal.close());
                });
        };

        $scope.openEditModal = (state) => {
            $scope.selectedState = angular.copy(state);
            stateEditModal.show();
        }

    });

}(jQuery));
