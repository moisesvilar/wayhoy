angular.module('angularApp').factory('IsAdminService', [
    'store',
    function(store) {

        var service = {};

        service.isAdmin = function() {
            return store.get('is_admin');
        };

        return service;
    }]);
