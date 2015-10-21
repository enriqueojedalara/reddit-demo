define(['layout/module'], function(module) {
	'use strict';
	return module.registerFactory('PostService', ['$http', '$q', 'StoreService', function($http, $q, StoreService) {
		return {
			submit: function(parameters){
				var parameters = parameters || {};
				var deferred = $q.defer();
				$http.post('/api/post/submit', parameters, StoreService.getHeaders()).then(function(res){
					deferred.resolve(res);
				}, function(error){
					deferred.reject(error.data);
				});
				return deferred.promise;
			},
			fetch: function(page){
				var deferred = $q.defer();
				$http.get('/api/posts/' + page, StoreService.getHeaders()).then(function(res){
					deferred.resolve(res);
				}, function(error){
					deferred.reject(error.data);
				});
				return deferred.promise;
			},
			vote: function(id, vote) {
                var deferred = $q.defer();
                var parameters = {id: id, vote: vote};
                $http.post('/api/post/vote', parameters, StoreService.getHeaders()).then(function(res) {
                    deferred.resolve(res);
                }, function(error) {
                    deferred.reject(error.data);
                });
                return deferred.promise;
            }
		}
	}]);
});