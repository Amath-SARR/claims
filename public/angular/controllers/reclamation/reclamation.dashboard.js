(function($) {
    angular.module('Claims').controller('ReclamationDashboardController',
        function($scope, $rootScope, Reclamation, User, State, Priorite, Comment, Application, CategorieReclamation) {
            $scope.reclamations = [];
            $scope.data = [];
            $scope.selectedReclamation = null;
            $scope.selectedApplication = null;
            $scope.selectedCategorieReclamation = null;
            $scope.application = null;
            $scope.selectedReclamationResponsables = {};
            $scope.users = [];
            $scope.priorites = {};
            $scope.states = {};
            $scope.viewOnlyMines = false;
            $scope.selectedResponsableIds = [];
            $scope.currentUserApplications = null;
            $scope.selectedApplicationId = 0;
            $scope.connectedUserApplications = [];
            $scope.categorieReclamations = [];
            $scope.newComment = {};
            $scope.answerComment = {};
            $scope.plus = false;

            var reclamationNewModalDashboard = new bootstrap.Modal($('#reclamationNewModalDashboard'), {
                keyboard: false
            });

            var reclamationDetailsModal = new bootstrap.Modal($('#detailsModal'), {
                keyboard: true
            });

            $('#detailsModal').on('hidden.bs.modal', function() {
                //lors de la fermeture du modal
                $scope.editSelectedPrioriteEnabled = false;
                $scope.editSelectedResponsableEnabled = false;
                $scope.editSelectedStateEnabled = false;
            });

            $scope.destroy = (reclamation) => {
                swal({
                    title: "Êtes-vous sur ?",
                    text: "Vous ne pourrez pas restaurer...",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Oui, Supprimer",
                    cancelButtonClass: "btn-primary",
                    cancelButtonText: 'Annuler',
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                },
                    function () {
                        Reclamation.destroy(reclamation)
                            .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                toastr.success(response.data);
                                reclamationDetailsModal.hide();
                                $scope.dashboard();
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors de la suppression de la réclamation");
                        })
                        .finally(() => swal.close());
                    });
            }

            $scope.getAllPriorites = () => {
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

            $scope.getAllStates = () => {
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

            $scope.getResponsablesSelectedReclamtion = () => {
                User.getResponsablesReclamation($scope.selectedReclamation.id)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.selectedReclamationResponsables = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });

            };

            $scope.getUsers = () => {
                User.getUsers()
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.users = response.data;
                            $scope.users.push({ photo_full_path: 'https://www.pngitem.com/pimgs/m/150-1503945_transparent-user-png-default-user-image-png-png.png', name: 'Non assigné', id: 0 })
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            $scope.getUsers();

            $scope.selectUserForFilter = (user) => {
                $scope.viewOnlyMines = false;
                user.selected = !user.selected;
                $scope.selectedResponsableIds = $scope.users.filter(user => user.selected)
                    .map(user => user.id);
                $scope.dashboard();
            };

            $scope.handleAppliationSelection = () => {
                $scope.dashboard();
            }

            $scope.viewOnlyMyReclamations = () => {
                $scope.viewOnlyMines = !$scope.viewOnlyMines;
                if ($scope.viewOnlyMines) {
                    $scope.users.forEach(user => user.selected = false);
                    $scope.selectedResponsableIds = [$rootScope.currentUser.id];
                } else {
                    $scope.selectedResponsableIds = [];
                }
                $scope.dashboard();
            };

            $scope.dashboard = () => {
                Reclamation.dashboard({ responsableIds: $scope.selectedResponsableIds, applicationId: $scope.selectedApplicationId })
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.data = response.data.reclamationMap;
                            $scope.currentUserApplications = response.data.currentUserApplications;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            $scope.dashboard();

            $scope.moveToState = async function(state, /*Reclamation*/ item) {
                if (item.state_id != state.id) {
                    item.state_id = state.id;
                    await Reclamation.moveToState(item)
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
                                $scope.dashboard();
                            } else {
                                return true;
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        });
                    return true;
                }
            };

            $scope.selectReclamation = (reclamation) => {
                $scope.selectedReclamation = reclamation;
                reclamationDetailsModal.show();
                $scope.findReclamation(reclamation);
            };

            $scope.findReclamation = (reclamation) => {
                Reclamation.show(reclamation.id)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.selectedReclamation = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.editSelectedPrioriteEnabled = false;
            $scope.enableSelectedPrioriteEdit = () => {
                $scope.editSelectedPrioriteEnabled = true;
                if (!$scope.priorites.length) {
                    $scope.getAllPriorites();
                }
                //   $('#priorite_edit_id').trigger('focus').trigger('click');
            };
            $scope.editSelectedResponsableEnabled = false;
            $scope.enableSelectedResponsableEdit = () => {
                $scope.editSelectedResponsableEnabled = true;
                $scope.getResponsablesSelectedReclamtion();
                // $('#user_edit_id').trigger('focus').trigger('click');
            };
            $scope.editSelectedStateEnabled = false;
            $scope.enableSelectedStateEdit = () => {
                $scope.editSelectedStateEnabled = true;
                if (!$scope.states.length) {
                    $scope.getAllStates();
                }
                // $('#state_edit_id').trigger('focuÒs').trigger('click');
            };

            $scope.updateSelectedReclamation = (currentUserId) => {
                if(currentUserId){
                    $scope.selectedReclamation.user_id = currentUserId;
                }
                Reclamation.update($scope.selectedReclamation).then((response) => {
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
                            $scope.dashboard();
                            $scope.getUsers();
                            $scope.selectedReclamation = response.data;
                            $scope.editSelectedPrioriteEnabled = false;
                            $scope.editSelectedResponsableEnabled = false;
                            $scope.editSelectedStateEnabled = false;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
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
                            $scope.dashboard();
                            reclamationNewModalDashboard.hide();
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

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
            }

            $scope.archiverReclamation = (reclamation) => {
                $scope.editionErrors = {};
                Reclamation.archive(reclamation)
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
                            $scope.selectedReclamation = response.data;
                            toastr.success("Réclamation archivée avec succès...");
                            $scope.dashboard();
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.removeComment = (comment) => {
                swal({
                        title: "Êtes-vous sur ?",
                        text: "Vous ne pourrez pas restaurer...",
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
                        Comment.destroy(comment)
                            .then((response) => {
                                response = response.data;
                                if (response.error) {
                                    toastr.error(response.data);
                                } else {
                                    $scope.findReclamation($scope.selectedReclamation);
                                    toastr.success('Commentaire supprimé avec succès...');
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    });
            };


            $scope.sendMailReclameur = () => {
                $scope.newComment.forReclameur = true;
            }
            $scope.addComment = (parent) => {
                if (parent) {
                    if($scope.answerComment) {
                        $scope.newComment = $scope.answerComment;
                    }
                    $scope.newComment.comment_id = parent.id;
                }
                $scope.newComment.reclamation_id = $scope.selectedReclamation.id;
                Comment.store($scope.newComment)
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
                            $scope.newComment = {};
                            $scope.answerComment = {};
                        } else {
                            if (parent) {
                                parent.subs.push(response.data);
                                parent.respond = false;
                            } else {
                                $scope.selectedReclamation.comments.unshift(response.data);
                            }
                            $scope.newComment = {};
                            $scope.answerComment = {};
                            $scope.updateSelectedReclamation();
                            toastr.success("Commentaire ajouté avec succès !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                        $scope.newComment = {};
                        $scope.answerComment = {};
                        $scope.updateSelectedReclamation();
                    });
            };

        });
}(jQuery));
