(function($) {
    angular.module('Claims').controller('ReclamationShowController',
        function($scope, $rootScope, $routeParams, Reclamation, User, State, Priorite, Comment) {
            $scope.priorites = [];
            $scope.states = [];
            $scope.users = [];
            $scope.reclamationResponsables = [];
            $scope.newComment = {};
            $scope.answerComment = {};
            $scope.reclamation = null;
            $scope.commentId = null;
            $scope.currentId = null;

            $scope.findReclamation = () => {
                Reclamation.show($routeParams.id)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.reclamation = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

            $scope.findReclamation();

            $scope.commentRespondForm = (id) => {
                if($scope.commentId) {
                    //if($scope.commentId!=id) {
                       let comment = document.getElementById('respondComment-'+$scope.commentId);
                        comment.classList.toggle("d-none");
                    //}
                }
                let comment = document.getElementById('respondComment-'+id);
                comment.classList.toggle("d-none");
                $scope.commentId = id;
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
                    $scope.reclamation.user_id = currentUserId;
                }
                Reclamation.update($scope.reclamation).then((response) => {
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
                            $scope.findReclamation();
                            $scope.getUsers();
                            $scope.reclamation = response.data;
                            $scope.editSelectedPrioriteEnabled = false;
                            $scope.editSelectedResponsableEnabled = false;
                            $scope.editSelectedStateEnabled = false;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };

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
                User.getResponsablesReclamation($scope.reclamation.id)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.reclamationResponsables = response.data;
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

            $scope.archiver = () => {
                Reclamation.archive($scope.reclamation)
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
                            $scope.reclamation = response.data;
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
                                    $scope.findReclamation($scope.reclamation);
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
                $scope.newComment.reclamation_id = $scope.reclamation.id;
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
                            $scope.findReclamation();
                            if (parent) {
                                parent.subs.push(response.data);
                                let commente = document.getElementById('respondComment-'+parent.id);
                                commente.classList.toggle("d-none");
                            } else {
                                $scope.reclamation.comments.unshift(response.data);
                            }
                            $scope.newComment = {};
                            $scope.answerComment = {};
                            toastr.success("Commentaire ajouté avec succès !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                        $scope.newComment = {};
                        $scope.answerComment = {};
                        $scope.findReclamation();
                    });

            };

        });

}(jQuery));
