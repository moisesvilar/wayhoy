angular.module('angularApp').factory('TokenService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/token';
        return $resource(URL);
    }]);
