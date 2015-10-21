define(['layout/module'], function (module) {
	"use strict";
	module.registerController('HeaderController', ['$scope', '$rootScope', '$location', 'UserService', 
		function ($scope, $rootScope, $location, UserService) {
			$rootScope.isAuthorized = false;
			UserService.isAuthorized().then(function(res){
				$rootScope.isAuthorized = true;
			},
			function(error){
				$rootScope.isAuthorized = false;
				//$location.path('/posts');
			});

			$scope.goHome = function() {
				$location.path('/posts');
			}
		}
	])
});
