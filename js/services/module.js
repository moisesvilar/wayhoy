angular.module('angularApp').factory('ModuleService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/module';
        return $resource(URL, null, {
            update: {
                method: 'PUT'
            }
        });
    }]);
