define(['layout/module'], function(module) {
    'use strict';
    module.registerController('IndexController', ['$scope', '$state', '$location', 'PostService', 'UserService', 
        function($scope, $state, $location, PostService, UserService) {

            $scope.init = function(){
                PostService.fetch($scope.page).then(function(res){
                    $scope.posts = res.data.posts;
                    $scope.votes = res.data.votes;
                },
                function(error){
                    console.log('Error', error);
                });
            }

            $scope.fetchMore = function(){
                PostService.fetch(parseInt($scope.page) + 1).then(function(res){
                    if (typeof res.data.posts == 'undefined') {
                        return;
                    }

                    if (res.data.posts.length > 0){
                        $scope.page = parseInt($scope.page) + 1;
                        $scope.posts = res.data.posts;
                        $scope.votes = res.data.votes;
                    }
                },
                function(error){
                    console.log('Error', error);
                })
            }

            $scope.fetchLess = function(){
                $scope.page = $scope.page - 1;
                if ($scope.page <= 0) {
                    $scope.page = 1;
                }
                PostService.fetch($scope.page).then(function(res){
                    if (typeof res.data.posts == 'undefined') {
                        return;
                    }

                    if (res.data.posts.length > 0){
                        $scope.posts = res.data.posts;
                        $scope.votes = res.data.votes;
                    }
                },
                function(error){
                    console.log('Error', error);
                })
            }

            $scope.vote = function($index, vote){
                var oldVote = $scope.votes[$scope.posts[$index].id];
                if ((vote > 0 && $scope.votes[$scope.posts[$index].id] > 0)
                    || (vote < 0 && $scope.votes[$scope.posts[$index].id] < 0)) {
                    vote = 0;
                }
                PostService.vote($scope.posts[$index].id, vote).then(function(res){

                    if (vote > 0){
                        $scope.votes[$scope.posts[$index].id] = 1
                    }
                    else if (vote < 0){
                        $scope.votes[$scope.posts[$index].id] = -1
                    }
                    else {
                        $scope.votes[$scope.posts[$index].id] = 0;
                    }
                    $scope.posts[$index].votes =  res.data.res;

                },
                function(error){
                    console.log('Error', error);
                    if (error.httpCode == 401){
                        alert('User is not logged, please login to the site');
                        $location.path('/signin');
                    }
                });
            }

            $scope.page = $state.params.page || 1;
            $scope.init($state.params.page);
        }
    ]);
});
