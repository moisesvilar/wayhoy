angular.module('angularApp').factory('AlbumImageService', [
    '$resource',
    '$http',
    'ACCESS_POINT',
    function($resource, $http, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/album/:album/image/:image';
        var service = $resource(URL, {album: '@album', image: '@image'}, {
            update: {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                }
            }
        });

        service.Url = function() {
            return URL;
        };

        return service;
    }]);
