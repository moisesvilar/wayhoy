angular.module('angularApp').factory('ScreenUsersService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/screen-users';
        return $resource(URL, {id: '@id', screen: '@screen'}, {
            assign: {
                method: 'POST'
            },
            unassign: {
                method: 'PUT'
            },
            visible: {
                method: 'PUT',
                params: {visible: 1}
            },
            invisible: {
                method: 'PUT',
                params: {invisible: 1}
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
