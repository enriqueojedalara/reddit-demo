define(['layout/module'], function(module) {
	'use strict';
	return module.registerFactory('UserService', ['$http', '$q', 'StoreService',
		function($http, $q, StoreService) {
			return {
				register: function(parameters){
					var parameters = parameters || {};
					var deferred = $q.defer();
					$http.post('/api/user', parameters).then(function(res){
						deferred.resolve(res);
					}, function(error){
						deferred.reject(error.data);
					});
					return deferred.promise;
				},
				login: function(parameters){
					var parameters = parameters || {};
					var deferred = $q.defer();
					$http.post('/api/login', parameters).then(function(res){
						deferred.resolve(res);
					}, function(error){
						deferred.reject(error.data);
					});
					return deferred.promise;
				},
				isAuthorized: function() {
					var headers = {
						headers: {
							'Authorization': StoreService.get('access_token')
						}
					};
					var deferred = $q.defer();
					$http.post('/api/login/status', {}, headers).then(function(res){
						deferred.resolve(res);
					}, function(error){
						deferred.reject(error.data);
					});
					return deferred.promise;
				}
			}
		}
	]);
});