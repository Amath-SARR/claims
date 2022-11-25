(function($) {
    angular.module('Claims').controller('CommentReclameurController',
        function($scope, $routeParams, Comment) {
            $scope.comment = null;
            $scope.newComment = {};

            $scope.findComment = () => {
                Comment.getComment($routeParams.uid)
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $scope.comment = response.data;
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
            $scope.findComment();

            $scope.addComment = () => {
                $scope.newComment.forReclameur = false;
                $scope.newComment.reclamation_id = $scope.comment.reclamation.id;
                $scope.newComment.comment_id = $scope.comment.id;
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
                        } else {
                            $scope.newComment = {};
                            toastr.success("Votre message a été bien envoyé à  l'équipe de Support !");
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
        })
}(jQuery));
