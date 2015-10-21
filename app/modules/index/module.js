define([
    'angular',
    'angular-couch-potato',
    'angular-ui-router',
], function (ng, couchPotato) {

    "use strict";

    var module = ng.module('app.index', [
        'ui.router',
    ]);

    couchPotato.configureApp(module);
    module.config(['$stateProvider', '$couchPotatoProvider', function ($stateProvider, $couchPotatoProvider){
        $stateProvider.state('app.main', {
            url: '/posts',
            views: {
                "content@app": {
                    templateUrl: 'app/modules/index/partials/main.html',
                    controller: 'IndexController',
                    resolve: {
                        deps: $couchPotatoProvider.resolveDependencies([
                            'modules/index/controllers/IndexController',
                            'modules/posts/services/PostService',
                            'modules/user/services/UserService',
                            'modules/index/filters/timeago',
                        ])
                    }
                },
            },
        })
        .state('app.pager', {
            url: '/posts/{page:[0-9]+}',
            views: {
                "content@app": {
                    templateUrl: 'app/modules/index/partials/main.html',
                    controller: 'IndexController',
                    resolve: {
                        deps: $couchPotatoProvider.resolveDependencies([
                            'modules/index/controllers/IndexController',
                            'modules/posts/services/PostService',
                            'modules/user/services/UserService',
                            'modules/index/filters/timeago',
                        ])
                    }
                },
            },
        })
    }]);

    module.run(['$couchPotato', function($couchPotato){
        module.lazy = $couchPotato;
    }]);
    
    return module;
});