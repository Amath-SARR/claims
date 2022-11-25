(function($) {
    angular.module('Claims').controller('ReclamationInsatisfactionController',
        function($scope, $routeParams, Reclamation,Comment) {
            $scope.reclamation = {};
            $scope.comment= {};
            $scope.newComment = {};
            $scope.reclamation_id = {};
            
            $scope.findReclamation = () => {
                Reclamation.getInsatisfactionReclamation($routeParams.uid)
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

            $scope.addComment = () => {
                $scope.newComment.forReclameur = false;
                $scope.newComment.reclamation_id = $scope.reclamation.id;
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
