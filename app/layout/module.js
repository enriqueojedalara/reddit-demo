define(['angular',
    'angular-couch-potato',
    'angular-ui-router'], function (ng, couchPotato) {

    "use strict";

    var module = ng.module('app.layout', ['ui.router']);
    couchPotato.configureApp(module);
    module.config(['$stateProvider', '$couchPotatoProvider', '$urlRouterProvider', 
        function ($stateProvider, $couchPotatoProvider, $urlRouterProvider) {
            $stateProvider
                .state('app', {
                    abstract: true,
                    views: {
                        root: {
                            templateUrl: 'app/layout/layout.html',
                            resolve: {
                                deps: $couchPotatoProvider.resolveDependencies([
                                    'layout/controllers/HeaderController',
                                    'modules/user/controllers/UserController',
                                    'layout/services/StoreService',
                                ])
                            }
                        }
                    }
                });
            $urlRouterProvider.otherwise('/posts');
        }
    ]);

    module.run(['$couchPotato', function ($couchPotato) {
        module.lazy = $couchPotato;
    }]);

    return module;

});
