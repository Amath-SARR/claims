(function($) {
    angular.module('Claims', ['datatables', 'ngRoute', 'angular.filter', 'ngDragDrop', 'angucomplete-alt',
            'ngSanitize', 'summernote', 'ui.select2', 'dndLists', 'frapontillo.bootstrap-switch',
            'angular-loading-bar', 'ngAnimate'
        ])
        .config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
            cfpLoadingBarProvider.includeSpinner = true;
            cfpLoadingBarProvider.latencyThreshold = 0;
        }])
        .run(($rootScope, Auth) => {
            // gerer métier global
            $rootScope.summerNoteOptions = { lang: 'fr-FR' };
            $rootScope.getConnectedUser = () => {
                Auth.getConnectedUser()
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            $rootScope.currentUser = response.data;
                        }
                    });
            };
            $rootScope.getConnectedUser();

            $rootScope.logout = () => {
                Auth.logout()
                    .then((response) => {
                        response = response.data;
                        if (response.error) {
                            toastr.error(response.data);
                        } else {
                            toastr.success("Vous êtes déconnecté(e) !");
                            window.location.href = '/';
                        }
                    })
                    .catch(() => {
                        toastr.error("Une erreur système s’est produite lors du traitement…");
                    });
            };
        })
        .config(['$routeProvider', ($routeProvider) => {
            $routeProvider.when('/', {
                templateUrl: 'angular/views/home.html',
                controller: 'HomeController'
            }).when('/state', {
                templateUrl: 'angular/views/state/state.html',
                controller: 'StateController'
            }).when('/profil', {
                templateUrl: 'angular/views/profil/profil.html',
                controller: 'ProfilController'
            }).when('/profil/:id', {
                templateUrl: 'angular/views/profil/profil.show.html',
                controller: 'ProfilShowController'
            }).when('/application', {
                templateUrl: 'angular/views/application/application.index.html',
                controller: 'ApplicationIndexController'
            }).when('/application/:id', {
                templateUrl: 'angular/views/application/application.show.html',
                controller: 'ApplicationShowController'
            }).when('/user', {
                templateUrl: 'angular/views/user/user.index.html',
                controller: 'UserIndexController'
            }).when('/user/:id', {
                templateUrl: 'angular/views/user/user.show.html',
                controller: 'UserShowController'
            }).when('/login', {
                templateUrl: 'angular/views/auth/login.html',
                controller: 'LoginController'
            }).when('/create-reclamation', {
                templateUrl: 'angular/views/reclamation/reclamation.new.html',
                controller: 'ReclamationNewController'
            }).when('/reclamation', {
                templateUrl: 'angular/views/reclamation/reclamation.index.html',
                controller: 'ReclamationIndexController'
            }).when('/reclamation/:id', {
                templateUrl: 'angular/views/reclamation/reclamation.show.html',
                controller: 'ReclamationShowController'
            }).when('/account', {
                templateUrl: 'angular/views/auth/account.html',
                controller: 'AccountController'
            }).when('/reclamation-dashboard', {
                templateUrl: 'angular/views/reclamation/reclamation.dashboard.html',
                controller: 'ReclamationDashboardController'
            }).when('/priorite', {
                templateUrl: 'angular/views/priorite/priorite.html',
                controller: 'PrioriteController'
            }).when('/password-reset/:token', {
                templateUrl: 'angular/views/auth/password.reset.html',
                controller: 'PasswordResetController'
            }).when('/comment/reclameur/:uid', {
                templateUrl: 'angular/views/comment/comment.reclameur.html',
                controller: 'CommentReclameurController'
            }).when('/reclamation/satisfaction/:uid', {
                templateUrl: 'angular/views/reclamation/reclamation.satisfaction.html',
                controller: 'ReclamationSatisfactionController'
            }).when('/reclamation/insatisfaction/:uid', {
                templateUrl: 'angular/views/reclamation/reclamation.insatisfaction.html',
                controller: 'ReclamationInsatisfactionController'
            }).otherwise({
                redirectTo: '/'
            });
        }]);
}(jQuery));

(function($) {
    $(".dropdown-item").on('click', function() {
        $('.dropdown').trigger('click');
    });
}(jQuery));

function convertImageToBase64String(event) {
    return new Promise((resolve, reject) => {
        var files = event.target.files;
        var file = files[0];
        if (files && file) {
            var reader = new FileReader();
            reader.onload = function(readerEvt) {
                var binaryString = readerEvt.target.result;
                let photoString = btoa(binaryString);
                resolve(photoString);
            };
            reader.readAsBinaryString(file);
        }
    });
}
