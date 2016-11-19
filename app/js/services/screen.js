angular.module('angularApp').factory('ScreenService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/screen';
        return $resource(URL, null, {
            update: {
                method: 'PUT'
            }
        });
    }]);
