angular.module('angularApp').factory('AlbumService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/album/:id';
        return $resource(URL, {id: '@id'}, {
            update: {
                method: 'PUT'
            }
        });
    }]);
