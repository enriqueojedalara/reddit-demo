define([
    'angular',
    'angular-couch-potato',
    'angular-ui-router',
], function (ng, couchPotato) {

    "use strict";

    var module = ng.module('app.posts', [
        'ui.router',
    ]);

    couchPotato.configureApp(module);
    module.config(['$stateProvider', '$couchPotatoProvider', function ($stateProvider, $couchPotatoProvider){
        $stateProvider.state('app.submit', {
            url: '/submit',
            views: {
                "content@app": {
                    templateUrl: 'app/modules/posts/partials/posts.html',
                    controller: 'PostController',
                    resolve: {
                        deps: $couchPotatoProvider.resolveDependencies([
                            'modules/posts/controllers/PostController',
                            'modules/posts/services/PostService',
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