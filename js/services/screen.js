angular.module('angularApp').factory('ScreenService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/screens/';
        return $resource(URL, {code: '@code'}, {
            update: {
                method: 'PUT'
            }
        });
    }]);
