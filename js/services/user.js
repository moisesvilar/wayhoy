angular.module('angularApp').factory('UserService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/users';
        return $resource(URL, null, {
            update: {
                method: 'PUT'
            }
        });
    }]);
