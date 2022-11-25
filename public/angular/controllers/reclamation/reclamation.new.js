(function($) {
    angular.module('Claims').controller('ReclamationNewController', function($scope, Profil, Reclamation, $location) {
        $scope.profils = [];
        $scope.selectedProfil = null;
        $scope.selectedApplication = null;
        $scope.newReclamation = null;
        $scope.selectedCategorieReclamation = null;

        $scope.findAllProfils = () => {
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
        $scope.findAllProfils();

        $scope.selectProfil = (profil) => {
            $scope.selectedProfil = profil;
            $scope.selectedApplication = null;
        };

        $scope.selectApplication = (application) => {
            $scope.selectedApplication = application;
        };

        $scope.selectCategorieReclamation = () => {
            $scope.selectedCategorieReclamation = $scope.selectedApplication.categorie_reclamations?.find((categorieReclamation) => categorieReclamation.id == $scope.newReclamation.categorie_reclamation_id);
            if ($scope.selectedCategorieReclamation) {
                if ($scope.selectedCategorieReclamation?.nom == 'Autre') {
                    $scope.newReclamation.objet = '';
                } else {
                    $scope.newReclamation.objet = $scope.selectedCategorieReclamation.nom;
                }
            }
        };

        $scope.store = () => {
            $scope.creationErrors = {};
            Reclamation.store($scope.newReclamation)
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
                        $scope.newReclamation = {};
                        toastr.success("Votre réclamation est envoyée à l'équipe support...");
                        $location.url('/');
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };


    });
}(jQuery));
