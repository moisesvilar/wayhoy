angular.module('angularApp').controller('ScreenController', [
    '$scope',
    '$location',
    '$routeParams',
    'store',
    'md5',
    'TokenService',
    'ScreenService',
    'IsAdminService',
    'AdminService',
    'USER',
    'TOKEN',
    function($scope, $location, $routeParams, store, md5, TokenService, ScreenService, IsAdminService, AdminService, USER, TOKEN) {

        $scope.model = {
            screens: [],
            code: '',
            name: ''
        };

        $scope.screen = null;
        $scope.admin = AdminService.get({id: store.get(USER)});

        var code = $routeParams.code || undefined;

        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            $scope.model.screens = ScreenService.query();
            if (code) {
                var user = store.get(USER);
                $scope.screen = ScreenService.get({code: code}, function(){
                    $scope.model.code = $scope.screen.code;
                    $scope.model.name = $scope.screen.name;
                });
            }
        });

        $scope.isAdmin = function() {
            return IsAdminService.isAdmin();
        };

        $scope.license = function() {
            if(confirm('¿Está seguro?')) {
                var name = $scope.model.name;
                ScreenService.update({code: code, name: name, isAdmin: IsAdminService.isAdmin(), licensed: '1'}, function(screen) {
                    jqalert('Pantalla ' + screen.name + ' (' + screen.code + ') actualizada correctamente');
                    $location.path('screen');
                }, function(err) {
                    handleError(err.status);
                });
            }
        };

        $scope.unlicense = function() {
            if(confirm('¿Está seguro?')) {
                var name = $scope.model.name;
                ScreenService.update({code: code, name: name, isAdmin: IsAdminService.isAdmin(), licensed: '0'}, function(screen) {
                    jqalert('Pantalla ' + screen.name + ' (' + screen.code + ') actualizada correctamente');
                    $location.path('screen');
                }, function(err) {
                    handleError(err.status);
                });
            }
        };

        $scope.freeLicenses = function() {
            var licenses = countLicensedScreen();
            return licenses < $scope.admin['total_licenses'];
        };

        $scope.submit = function() {
            cleanErrors();
            var name = $scope.model.name;
            var code = $scope.model.code;
            if (!IsAdminService.isAdmin() && !code.trim()) return error('errorCode', 'Debes introducir el código que se muestra en la pantalla');
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                ScreenService.save({code: code, name: name, isAdmin: IsAdminService.isAdmin()}, function(screen) {
                    jqalert('Pantalla ' + screen.name + ' (' + screen.code + ') creada correctamente');
                    $location.path('screen');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.update = function() {
            cleanErrors();
            var name = $scope.model.name;
            var code = $scope.model.code;
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                ScreenService.update({code: code, newcode: $scope.screen.code, name: name, isAdmin: IsAdminService.isAdmin()}, function(screen) {
                    jqalert('Pantalla ' + screen.name + ' (' + screen.code + ') actualizada correctamente');
                    $location.path('screen');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.remove = function() {
            $('#your-modal-id').modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                ScreenService.remove({code: code}, function() {
                    jqalert('Pantalla ' + $scope.screen.name + ' eliminada correctamente');
                    $location.path('screen');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        function error(field, msg) {
            $scope.model[field] = msg;
        }

        function handleError(code) {
            switch (code) {
                case 400: return alert('La petición es incorrecta. Falta algún parámetro obligatorio.');
                case 403: return alert('Credenciales de acceso incorrectas. Cierre sesión y vuelva a acceder con su email y contraseña.');
                case 404: return alert('El album o la pantalla no existen.');
                case 405: return alert('El método HTTP no está soportado.');
                case 500: return alert('Error interno. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
                default: return alert('Error desconocido. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
            }
        }

        function cleanErrors() {
            $scope.model.errorName = false;
        }

        function countLicensedScreen() {
            return $scope.model.screens.length;
        }
    }
]);