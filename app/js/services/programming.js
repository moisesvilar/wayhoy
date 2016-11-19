angular.module('angularApp').factory('ProgrammingService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/mobile/programming.php';
        return $resource(URL, {id: '@id'}, {
            update: {
                method: 'PUT'
            }
        });
    }]);
