angular.module('angularApp').factory('AlbumUsersService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/album-users';
        return $resource(URL, {id: '@id', album: '@album'}, {
            assign: {
                method: 'POST'
            },
            unassign: {
                method: 'PUT'
            },
            getUsers: {
                method: 'GET',
                isArray: true
            },
            getAssignedUsers: {
                method: 'GET',
                isArray: true,
                params: {assigned: 1}
            }
        });
    }]);
