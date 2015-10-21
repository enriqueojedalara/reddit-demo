/**
 * Main module
 */
'use strict';

define([
	'angular',
	'angular-couch-potato',
	'angular-ui-router',
	'angular-cookies',
], function (ng, couchPotato) {

	var app = ng.module('app', [
		'scs.couch-potato',
		'ui.router',
		'ngCookies',

		'app.layout',
		'app.index',
		'app.posts',
		'app.user',
	]);

	couchPotato.configureApp(app);

	app.config(['$provide', '$httpProvider', function ($provide, $httpProvider) {
		$provide.factory('ErrorHttpInterceptor', function ($q) {
			var errorCounter = 0;
			return {
				requestError: function (rejection) {
					return $q.reject(rejection);
				},

				responseError: function (rejection) {
					return $q.reject(rejection);
				}
			};
		});
		$httpProvider.interceptors.push('ErrorHttpInterceptor');
	}]);

	app.config(['$locationProvider', function($locationProvider) {
		$locationProvider.html5Mode(true).hashPrefix('!');;
	}]);

	app.run(['$couchPotato', '$rootScope', '$state', '$stateParams', function ($couchPotato, $rootScope, $state, $stateParams){
		app.lazy = $couchPotato;
		$rootScope.$state = $state;
		$rootScope.$stateParams = $stateParams;
	}]);

	return app;
});
