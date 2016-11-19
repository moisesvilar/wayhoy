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
        //$scope.model.isLogin = false;
        $scope.isHome = $rootScope.location.path() == 'album/';

        var remember = store.get(REMEMBER);
        if (remember) {
            var user = store.get(USER);
            var pass = store.get(PASSWORD);
            LoginService.login(user, pass, remember, function(err) {
                if (err) {
                    $location.path('home');
                    return alert(err);
                }else{
                    loginUI();
                }
            });
        }

        $scope.login = function() {
            //LoginService.login($scope.model.email, md5.createHash($scope.model.password), $scope.model.remember, function(err) {
            LoginService.login($scope.model.email, $scope.model.password, $scope.model.remember, function(err, data) {
                if (err) {
                    $location.path('home');
                    return alert(err);
                }else{
                    if(data.is_admin) store.set('is_admin', true);
                    loginUI();
                }

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

        function checkidR(){
            var user = store.get('wayhoy_user');
            var pass = store.get('wayhoy_pass');
            var hash = cryptofoo.hash('md5', user + pass );
            var addUrl = apoint+'luk/checkidR.php?u=' + encodeURIComponent(user) + '&t=' + hash;
            $.get(addUrl, function(data){
                var str1 = $.trim(data.split(':')[0]);
                if(str1=='dual-link'){
                    sessionStorage.setItem('user_id_r', str1);
                }
            });
        }
        function loginUI() {
            $scope.model.isLogin = true;
            $scope.model.collapsed = true;
            checkidR();
            $location.path('album');
        }

        function logoutUI() {
            $scope.model.isLogin = false;
            $scope.model.collapsed = false;
        }
        function handleError(code) {
            switch (code) {
                case 400: return jqalert('petición incorrecta. Falta algún par\u00E1metro obligatorio.');
                case 403: return jqalert('credenciales de acceso incorrectas.');
                case 404: return jqalert('\u00E1lbum o la pantalla no existen.');
                case 405: return jqalert('método no soportado.');
                case 500: return jqalert('Erro interno. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
                case 409: return jqalert('Deco asociado a otro cliente');
                default: return jqalert('Error desconocido. Inténtelo de nuevo pasados unos minutos.');
            }
        }

        function cleanErrors() {
            $scope.model.errorName = false;
        }

    }]);