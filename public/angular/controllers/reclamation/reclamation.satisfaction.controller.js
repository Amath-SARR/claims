(function($) {
    angular.module('Claims').controller('ReclamationSatisfactionController',
        function($scope, $routeParams, Reclamation) {
            $scope.reclamation = {};

            $scope.findReclamation = () => {
                Reclamation.getSatisfactionReclamation($routeParams.uid)
                .then((response)=> {
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
        })
}(jQuery));
