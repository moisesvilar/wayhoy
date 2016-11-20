angular.module('angularApp').factory('LoginService', [
    '$http',
    'md5',
    'store',
    'TokenService',
    'ACCESS_POINT',
    'USER',
    'PASSWORD',
    'TOKEN',
    'REMEMBER',
    function($http, md5, store, TokenService, ACCESS_POINT, USER, PASSWORD, TOKEN, REMEMBER) {
        var URL = ACCESS_POINT + '/login';
        var user = null;
        var pass = null;

        var service = {};

        service.login = function(email, password, remember, callback) {
            TokenService.get(function(json) {
                if (!json.token) {
                    service.login(email, password, callback);
                    return;
                }
                var token = json.token;
                store.set(TOKEN, token);
                if(remember) store.set(REMEMBER, remember);
                if(email) store.set(USER, email);
                if(email) cookie.set(USER, email);
                if(password) store.set(PASSWORD, password);
                if(password) cookie.set(PASSWORD, password);
                $http.get(URL, {handleError: true}).
                    success(function (data) {
                        callback(null, data);
                    }).
                    error(function (err, status) {
                        alert('Usuario ou contrasinal incorrectos');
                        store.remove(USER);
                        cookie.remove(USER);
                        store.remove(PASSWORD);
                        cookie.remove(PASSWORD);
                        store.remove(REMEMBER);
                        return callback(status);
                    });
            });
        };

        return service;
}]);
