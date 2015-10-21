define(['layout/module'], function(module) {
    'use strict';
    module.registerController('PostController', ['$scope', '$location', 'PostService', 
        function($scope, $location, PostService) {
        	$scope.submit = function(){
        		PostService.submit($scope.post).then(function(res){
                    $location.path('/posts');
                }),
                function(error){
                    console.log('Error in PostController');
                }
        	}
        }
    ]);
});