(function($) {
    angular.module('Claims').controller('UserIndexController', function($scope, User) {
        $scope.users = [];
        $scope.selectedUser = null;
        $scope.newUser = {};
        $scope.creationErrors = {};
        $scope.editionErrors = {};

        $scope.types = [{ code: 'Administrateur', label: 'Administrateur' }, { code: 'Gestionnaire', label: 'Gestionnaire' }];
        var userEditModal = new bootstrap.Modal($('#userEditModal'), {
            keyboard: false
        });
        $scope.index = () => {
            User.index()
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.users = response.data;
                        $scope.users.forEach(function(user) {
                            if (user.last_login_at) {
                                user.last_login_at_format = new Date(user.last_login_at);
                            }
                        });
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };
        $scope.index();

        $scope.store = () => {
            $scope.creationErrors = {};
            User.store($scope.newUser)
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
                        response.data.enabled = 1;
                        $scope.users.unshift(response.data);
                        toastr.success('Utilisateur créé avec succès...')
                        $scope.newUser = {};
                        $('#photoInput').val(null);
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.update = () => {
            $scope.editionErrors = {};
            User.update($scope.selectedUser)
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
                        userEditModal.hide();
                        toastr.success("Mis à jour avec succès !");
                        $scope.index();
                        $scope.selectedUser = {};
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        };

        $scope.destroy = (user) => {
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
                    User.destroy(user)
                        .then((response) => {
                            response = response.data;
                            if (response.error) {
                                toastr.error(response.data);
                            } else {
                                $scope.index();
                                toastr.success('Utilisateur supprimé avec succès...');
                            }
                        })
                        .catch(() => {
                            toastr.error("Une erreur système s’est produite lors du traitement…");
                        })
                        .finally(() => swal.close());
                });
        };

        $scope.openEditModal = (user) => {
            $scope.selectedUser = angular.copy(user);
            userEditModal.show();
        };

        $('#photoInput').on('change', (evt) => {
            convertImageToBase64String(evt)
                .then((data) => {
                    $scope.newUser.photo = data;
                })
                .catch(() => {});
        });


        $scope.selectPhotoEdit = (user) => {
            $scope.selectedUser = user;
            angular.element('#photoInputEdit').trigger('click');
        };


        $('#photoInputEdit').on('change', (evt) => {
            convertImageToBase64String(evt)
                .then((data) => {
                    $scope.selectedUser.photo = data;
                    $scope.updatePhoto($scope.selectedUser);
                })
                .catch(() => {});
        });

        $scope.updatePhoto = (selectedUser) => {
            User.updatePhoto(selectedUser)
                .then((response) => {
                    response = response.data;
                    if (response.error) {
                        toastr.error(response.data);
                    } else {
                        $scope.selectedUser.photo_full_path = response.data.photo_full_path;
                        toastr.success("Succès !");
                    }
                })
                .catch(() => {
                    toastr.error("Une erreur système s’est produite lors du traitement…");
                });
        }

        $scope.swicthUserEnablement = (user) => {
            $scope.editionErrors = {};
            swal({
                    title: "Êtes-vous sur ?",
                    text: "Vous allez changer l'état de ce compte...",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: `${user.enabled?'Activer':'Désactiver'}`,
                    cancelButtonClass: "btn-dark",
                    cancelButtonText: 'Annuler',
                    closeOnConfirm: false,
                },
                function(confirm) {
                    if (confirm) {
                        User.update(user)
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
                                    if (user.enabled == 1) {
                                        toastr.success("Compte activé avec succès...");
                                    } else {
                                        toastr.success("Compte désactivé avec succès...");
                                    }
                                }
                            })
                            .catch(() => {
                                toastr.error("Une erreur système s’est produite lors du traitement…");
                            })
                            .finally(() => swal.close());
                    } else {
                        $scope.index();
                    }
                });
        }
    });

}(jQuery));
