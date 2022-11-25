(function($) {
    angular.module('Claims').controller('ReclamationIndexController', function($scope, Reclamation, State, Application, $location, CategorieReclamation) {
        $scope.reclamations = [];
        $scope.selectedReclamation = null;
        $scope.newReclamation = {};
        $scope.creationErrors = {};
        $scope.editionErrors = {};
        $scope.paginationData = {};
        $scope.selectedPageSizeOption = 20;
        $scope.applications = [];
        $scope.states = [];
        $scope.selectedApplicationId = 0;
        $scope.selectedStateId = 0;
        $scope.refreshReclamationList = 0;
        $scope.connectedUserApplications = [];
        $scope.categoriesReclamations = [];
        $scope.selectedApplication = null;
        $scope.selectedCategorieReclamation = null;
        $scope.application = null;
        var reclamationNewModalIndex = new bootstrap.Modal($('#reclamationNewModalIndex'), {
            keyboard: false
        });

        $scope.handleAppliationAndStateSelection = () => {
            $scope.changePageSize();
        }

        $scope.getAllStates = () => {
            if ($scope.states.length < 1) {
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
            }
        }

        $scope.getAuthUserApplications = () => {
            if ($scope.applications.length < 1) {
                Application.getAuthUserApplications()
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

        $scope.changePageSize = (refreshReclamationList) => {
            if (refreshReclamationList == 0) {
                $scope.selectedApplicationId = 0;
                $scope.selectedStateId = 0;
            }
            Reclamation.paginate({ selectedPageSizeOption: $scope.selectedPageSizeOption, applicationId: $scope.selectedApplicationId, stateId: $scope.selectedStateId })
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.paginationData = response.data;
                        $scope.totalItems = response.data.total;
                        if ($scope.totalItems % $scope.selectedPageSizeOption == 0) {
                            $scope.pagesNumber = $scope.totalItems / $scope.selectedPageSizeOption;
                        } else {
                            $scope.pagesNumber = ~~($scope.totalItems / $scope.selectedPageSizeOption) + 1;
                        }
                        $scope.reclamations = response.data.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        }
        $scope.changePageSize();

        $scope.changePagination = (pageNumber) => {
            Reclamation.paginate($scope.selectedPageSizeOption, pageNumber)
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.paginationData = response.data;
                        $scope.reclamations = response.data.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.goPreviousOrFollowingPage = (pageNumber) => {
            Reclamation.paginate(parseInt($scope.paginationData.per_page), pageNumber)
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.paginationData = response.data;
                        $scope.reclamations = response.data.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.open = (reclamation) => {
            $location.url('/reclamation/' + reclamation.id);
        };

        //Création d'une nouvelle réclamation
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
                        $scope.changePageSize();
                        $scope.changePagination();
                        reclamationNewModalIndex.hide();
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        //Applications auxquelles l'utilisateur a accès
        $scope.getConnectedUserApplications = () => {
            Application.getAuthUserApplications()
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.connectedUserApplications = response.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };
        $scope.getConnectedUserApplications();

        $scope.selectApplication = (application) => {
            $scope.selectedApplication = application;
        };

        //Categories Réclamations auxquelles l'utilisateur a accès
        $scope.getConnectedUserCategorieReclamations = () => {
            CategorieReclamation.getAuthUserCategoriesReclamation()
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.categoriesReclamations = response.data;
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };
        $scope.getConnectedUserCategorieReclamations();

        $scope.selectCategorieReclamation = () => {
            $scope.selectedCategorieReclamation = $scope.categoriesReclamations.find((categorieReclamation) => categorieReclamation.id == $scope.newReclamation.categorie_reclamation_id);
            if ($scope.selectedCategorieReclamation) {
                if ($scope.selectedCategorieReclamation.nom == 'Autre') {
                    $scope.newReclamation.objet = '';
                } else {
                    $scope.newReclamation.objet = $scope.selectedCategorieReclamation.nom;
                }
            }
        };


    });
}(jQuery));
