angular.module('angularApp').factory('ImageService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/image/:id';
        var service = $resource(URL, {id: '@id'}, {
            update: {
                method: 'PUT'
            },
            galleryr: {
                method: 'GET',
                params: {galleryr: 1},
                isArray: true
            },
            albumimage: {
                method: 'GET',
                params: {albumimage: 1}
            }
			
        });
		
        service.Url = function() {
            return URL;
        };

        return service;
    }]);
