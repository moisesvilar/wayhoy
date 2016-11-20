angular.module('angularApp').factory('PermissionsService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/permissions';
        return $resource(URL, null, {
            update: {
                method: 'PUT'
            }
        });
    }]);
