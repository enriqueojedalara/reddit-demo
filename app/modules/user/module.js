define([
    'angular',
    'angular-couch-potato',
    'angular-ui-router',
], function (ng, couchPotato) {

    "use strict";

    var module = ng.module('app.user', [
        'ui.router',
    ]);

    couchPotato.configureApp(module);
    module.config(['$stateProvider', '$couchPotatoProvider', function ($stateProvider, $couchPotatoProvider){
        $stateProvider.state('app.signup', {
            url: '/signup',
            views: {
                "content@app": {
                    templateUrl: 'app/modules/user/partials/signup.html',
                    controller: 'UserController',
                },
            },
            resolve: {
                deps: $couchPotatoProvider.resolveDependencies([
                    'modules/user/services/UserService',
                    'modules/user/controllers/UserController',
                ])
            },
        })
        .state('app.signin', {
            url: '/signin',
            views: {
                "content@app": {
                    templateUrl: 'app/modules/user/partials/signin.html',
                    controller: 'UserController',
                },
            },
            resolve: {
                deps: $couchPotatoProvider.resolveDependencies([
                    'layout/services/StoreService',
                    'modules/user/services/UserService',
                    'modules/user/controllers/UserController',
                ])
            },
        })
        .state('app.logout', {
            url: '/logout',
            views: {
                "content@app": {
                    templateUrl: 'app/modules/user/partials/logout.html',
                    controller: 'UserController',
                },
            },
            resolve: {
                deps: $couchPotatoProvider.resolveDependencies([
                    'layout/services/StoreService',
                    'modules/user/services/UserService',
                    'modules/user/controllers/UserController',
                ])
            },
        })
    }]);

    module.run(['$couchPotato', function($couchPotato){
        module.lazy = $couchPotato;
    }]);
    
    return module;
});