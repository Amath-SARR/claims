(function($) {
    angular.module('Claims').controller('ProfilShowController',
        function($scope, Profil, $routeParams, Application, ApplicationProfil) {
            $scope.profil = {};
            $scope.applications = [];

            $scope.show = () => {
                Profil.show($routeParams.id)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.profil = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            $scope.show();

            $scope.findAllApplications = () => {
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
            $scope.findAllApplications();

            $scope.linkProfilToApplication = (application) => {
                let applicationProfil = { profil_id: $routeParams.id, application_id: application.id };
                ApplicationProfil.store(applicationProfil)
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
                            $scope.profil.application_profils.unshift(response.data);
                            toastr.success("Application rattachée au profil !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.destroyApplicationProfil = (applicationProfil) => {
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
                        ApplicationProfil.destroy(applicationProfil)
                            .then((response) => {
                                toastr.success('Application supprimée avec succès...');
                                response = response.data;
                                if (response.error) {
                                    toastr.error(response.data);
                                } else {
                                    $scope.show();
                                    toastr.success('Application supprimée avec succès...');
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };
        });

}(jQuery));
