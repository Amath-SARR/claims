(function($) {
    angular.module('Claims').controller('ApplicationShowController',
        function($scope, $window, $routeParams, Application, User, Profil, Intervenant,
            ApplicationProfil, CategorieReclamation, Gestionnaire, $rootScope) {
            $scope.application = {};
            $scope.selectedApplication = null;
            $scope.users = [];
            $scope.profils = [];
            $scope.selectedUserToAddAsIntervenant = null;
            $scope.intervenantCreationErrors = null;
            $scope.newCategorieReclamation = {};
            $scope.selectedCategorieReclamation = null;
            $scope.categorieReclamationCreationErrors = null;
            $scope.categorieReclamationEditionErrors = null;
            $scope.selectedUserToAddAsGestionnaire = { id: null };
            var applicationEditModal = new bootstrap.Modal($('#applicationEditModal'), {
                keyboard: false
            });

            $scope.show = () => {
                Application.show($routeParams.id)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.application = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            $scope.show();

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
                    .catch(() => {})
            });

            $scope.updateLogo = (selectedApplication) => {
                Application.updateLogo(selectedApplication)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.selectedApplication.logo_full_path = response.data.logo_full_path;
                            toastr.success('Logo mis à jour !');
                        }

                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.findAllUsers = () => {
                if ($scope.users.length == 0) {
                    User.index()
                        .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                $scope.users = response.data;
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        });
                }
            }

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
            }

            $scope.$watch('selectedUserToAddAsIntervenant', ($event) => {
                if ($event) {
                    let user = $event.description;
                    let intervenant = { user_id: user.id, application_id: $routeParams.id };
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
                                $scope.show();
                                $scope.application.intervenants = [...$scope.application.intervenants, response.data];
                                toastr.success('Intervenant ajouté');
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        });
                }
            });

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
                                    toastr.success('Intervenant supprimé !!!');
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };

            $scope.linkProfilToApplication = (profil) => {
                let applicationProfil = { profil_id: profil.id, application_id: $routeParams.id };
                ApplicationProfil.store(applicationProfil)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            if (response.validationError) {
                                Object.values(response.data).forEach((error) => {
                                    toastr.error(error);
                                });
                            } else {
                                toastr.error(response.data);
                            }
                        } else {
                            $scope.application.application_profils.unshift(response.data);
                            toastr.success("Profil rattaché à l'application...");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            }

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
                                response = response.data;
                                if (response.error) {
                                    toastr.error(response.data);
                                } else {
                                    $scope.show();
                                    toastr.success("Profil supprimé de l'application !");
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };

            $scope.destroyApplication = () => {
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
                        Application.destroy($scope.application)
                            .then((response) => {
                                response = response.data;
                                if (response.error) {
                                    toastr.error(response.data);
                                } else {
                                    $scope.show();
                                    toastr.success(" Application supprimée!");
                                    $window.location.href = '/#!/application';
                                }
                            })
                            .catch(() => {
                                toastr.error("Suppression impossible! Cette application contient des catégories de réclamations. Veuillez d'abord les supprimer. ");
                            })
                            .finally(() => swal.close());
                    });
            };
            // gestion des catégories application
            var categorieReclamationEditModal = new bootstrap.Modal($('#categorieReclamationEditModal'), {
                keyboard: false
            });

            $scope.storeCategorieReclamation = () => {
                $scope.categorieReclamationCreationErrors = {};
                $scope.newCategorieReclamation.application_id = $routeParams.id;
                CategorieReclamation.store($scope.newCategorieReclamation)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            if (response.validationError) {
                                $scope.categorieReclamationCreationErrors = response.data;
                                Object.values($scope.categorieReclamationCreationErrors).forEach((error) => {
                                    toastr.error(error)
                                });
                            } else {
                                toastr.error(response.data);
                            }
                        } else {
                            $scope.application.categorie_reclamations.unshift(response.data);
                            $scope.newCategorieReclamation = {};
                            toastr.success("Catégorie réclamation créée");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.updateCategorieReclamation = () => {
                $scope.categorieReclamationEditionErrors = {};
                CategorieReclamation.update($scope.selectedCategorieReclamation)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            if (response.validationError) {
                                $scope.categorieReclamationEditionErrors = response.data;
                                Object.values($scope.categorieReclamationEditionErrors).forEach((error) => {
                                    toastr.error(error)
                                });
                            } else {
                                toastr.error(response.data);
                            }
                        } else {
                            categorieReclamationEditModal.hide();
                            $scope.show();
                            $scope.selectedCategorieReclamation = null;
                            toastr.success("Catégorie mise à jour !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.destroyCategorieReclamation = (categorieReclamation) => {
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
                        CategorieReclamation.destroy(categorieReclamation)
                            .then((response) => {
                                toastr.success('Catégorie de réclamation supprimée avec succès...');
                                response = response.data;
                                if (response.error) {
                                    toastr.error(response.data);
                                } else {
                                    $scope.show();
                                    toastr.success("Catégorie réclamation supprimée !");
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };

            $scope.openCategorieReclamationEditModal = (categorieReclamation) => {
                $scope.selectedCategorieReclamation = categorieReclamation;
                categorieReclamationEditModal.show();
            };

            // implémenter le crud des gestionnaires
            $scope.addNewGestionnaire = (categorieReclamation) => {
                let gestionnaire = { user_id: $scope.selectedUserToAddAsGestionnaire.id, categorie_reclamation_id: categorieReclamation.id };
                $scope.gestionnaireCreationErrors = null;
                Gestionnaire.store(gestionnaire)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            if (response.validationError) {
                                $scope.gestionnaireCreationErrors = response.data;
                                Object.values($scope.gestionnaireCreationErrors).forEach((error) => {
                                    toastr.error(error)
                                });
                            } else {
                                toastr.error(response.data);
                            }
                        } else {
                            categorieReclamation.gestionnaires.unshift(response.data);
                            $scope.selectedUserToAddAsGestionnaire.id = null;
                            $scope.gestionnaireCreationErrors = null;
                            toastr.success("Gestionnaire ajouté !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
                $('#newGestionnaireSelectField').val('');
            };

            $scope.destroyGestionnaire = (gestionnaire) => {
                if(!$rootScope.currentUser.is_admin) {
                    return ;
                }
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
                                    toastr.success('Gestionnaire supprimé avec succès...');
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };

            $scope.openEditModal = () => {
                applicationEditModal.show();
            };

            $scope.update = () => {
                $scope.editionErrors = {};
                Application.update($scope.application)
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
                            $scope.index();
                            $scope.selectedApplication = {};
                            toastr.success("Application mise à jour !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            // fin du controller function
        });


}(jQuery));
