angular.module('angularApp').factory('RecoveryService', [
    '$resource',
    'ACCESS_POINT',
    function($resource, ACCESS_POINT) {
        var URL = ACCESS_POINT + '/mobile/recovery.php';
        return $resource(URL);
    }]);
