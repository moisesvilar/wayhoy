angular.module('angularApp').factory('AdminService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/admin_info';
        return $resource(URL, {id: '@id'}, {
        });
    }]);
