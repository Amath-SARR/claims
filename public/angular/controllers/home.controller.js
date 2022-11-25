(function($) {
    angular.module('Claims').controller('HomeController', function($scope, Application) {
        $scope.applications = [];

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
    });
}(jQuery));
