angular.module('angularApp').controller('LoginController', [
    '$scope',
    '$rootScope',
    '$location',
    'md5',
    'store',
    'LoginService',
    'IsAdminService',
    'PermissionsService',
    'USER',
    'PASSWORD',
    'REMEMBER',
    function($scope, $rootScope, $location, md5, store, LoginService, IsAdminService, PermissionsService, USER, PASSWORD, REMEMBER) {

        $scope.model = {
            email: undefined,
            password: undefined,
            remember: false,
            collapsed: true,
            isLogin: false
        };

        $scope.isHome = $rootScope.location.path() == '/';

        var remember = store.get(REMEMBER);
        if (remember) {
            var user = store.get(USER);
            var pass = store.get(PASSWORD);
            LoginService.login(user, pass, remember, function(err) {
                if (err) {
                    $location.path('home');
                    return console.error(err);
                }
                loginUI();
                $location.path('album');
            });
        }

        var permissions = PermissionsService.query();
        $scope.permission = function(which) {
            if (permissions && permissions.length > 0) {
                return permissions[0][which] === '1';
            }
            return false;
        };

        $scope.login = function() {
            LoginService.login($scope.model.email, $scope.model.password, $scope.model.remember, function(err, data) {
                if (err) {
                    $location.path('home');
                    return console.error(err);
                }
                if(data.is_admin) store.set('is_admin', true);
                loginUI();
                $location.path('album');
            });
        };

        $scope.closeSession = function() {
            store.set(USER, undefined);
            store.set(PASSWORD, undefined);
            store.set(REMEMBER, undefined);
            store.set('is_admin', undefined);
            logoutUI();
            $location.path('home');
        };

        $scope.isAdmin = function() {
            return IsAdminService.isAdmin();
        };

        function loginUI() {
            $scope.model.isLogin = true;
            $scope.model.collapsed = true;
        }

        function logoutUI() {
            $scope.model.isLogin = false;
            $scope.model.collapsed = true;
        }

}]);