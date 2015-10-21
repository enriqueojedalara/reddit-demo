define(['layout/module', 'angular-cookies'], function(module) {
    'use strict';
    return module.registerFactory('StoreService', ['$cookieStore', function($cookieStore) {
        return {
            set: function(key, value) {
                $cookieStore.put(key, value);
            },
            get: function(key) {
                return $cookieStore.get(key);
            },
            remove: function(key) {
                $cookieStore.remove(key);
            },
            getHeaders: function() {
                return {
                    headers: {
                        'Authorization': this.get('access_token')
                    }
                }
            }
        }
    }]);
});