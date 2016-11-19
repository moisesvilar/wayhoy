angular.module('angularApp').factory('TokenInterceptor', [
    '$rootScope',
    'store',
    'md5',
    'USER',
    'PASSWORD',
    'TOKEN',
    function($rootScope, store, md5, USER, PASSWORD, TOKEN) {
        var interceptor = this;

        interceptor.request = function(config) {
            config.headers = config.headers || {};
            var user = store.get(USER);
            var pass = store.get(PASSWORD);
            var token = store.get(TOKEN);
            if (config.headers['Content-Type'] != undefined) {
                config.headers['Content-Type'] = 'application/json; charset=utf-8';
            }
            if (user && pass && token) {
                config.headers['User'] = user;
                config.headers['Secret'] = pass;
            }
            return config;
        };

        return interceptor;
    }
]);
