define(['layout/module'], function(module) {
    "use strict";
    module.registerController('UserController', ['$scope', '$rootScope', '$location', 'StoreService', 'UserService', 
    	function($scope, $rootScope, $location, StoreService, UserService) {
	        $scope.register = function() {
	        	UserService.register($scope.user).then(function(res){
	        		alert('User has created, you can use it right now');
	        		$location.path('/signin');
	        	},
	        	function(error){
	        		alert(error.httpError);
	        		console.log(error);
	        	});
	        }

		    $scope.login = function() {
	        	UserService.login($scope.user).then(function(res){
	        		$rootScope.isAuthorized = true;
	        		StoreService.set('access_token', res.data.access_token);
	        		StoreService.set('refresh_token', res.data.refresh_token);
	        		$location.path('/posts');
	        	},
	        	function(error){
	        		alert(error.httpError);
	        		console.log(error);
	        	});
	        }

	        $scope.logout = function() {
	        	$rootScope.isAuthorized = false;
	        	StoreService.remove('access_token');
        		StoreService.remove('refresh_token');
        		$location.path('/posts');
	        }

	        if ($location.path() == '/logout'){
	        	$scope.logout();
	        }
	    }
	]);
});