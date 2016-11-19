angular.module('angularApp').controller('ScreenUsersController', [
    '$scope',
    '$routeParams',
    '$location',
    'TokenService',
    'ScreenUsersService',
    'IsAdminService',
    'ScreenService',
    function($scope, $routeParams, $location, TokenService, ScreenUsersService, IsAdminService, ScreenService) {

        if(!IsAdminService.isAdmin()) {
            $location.path('');
        }

        $scope.model = {
            users: [],
            assigned_users: [],
            email_user: null
        };

        $scope.screen = null;

        var code = $routeParams.screen || undefined;
        $scope.model.users = ScreenUsersService.getUsers({screen: code}, function() {
            $('.select2').select2();
        });
        $scope.screen = ScreenService.get({code: code});
        $scope.model.assigned_users = ScreenUsersService.getAssignedUsers({screen: code});

        $scope.assign = function() {
            if(!$scope.model.email_user) return;
            ScreenUsersService.assign({
                screen: code,
                user: $scope.model.email_user
            }, function(user) {
                jqalert('Usuario ' + user.name + ' asignado a la pantalla ' + $scope.screen.name + ' correctamente');
                $scope.model.assigned_users = ScreenUsersService.getAssignedUsers({screen: code});
                $scope.model.users = ScreenUsersService.getUsers({screen: code}, function() {
                    $('.select2').select2();
                });
            }, function(err) {
                handleError(err.status);
            });
        };

        $scope.unassing = function(email_user) {
            if(confirm('¿Está seguro?')) {
                ScreenUsersService.unassign({
                    screen: code,
                    user: email_user
                }, function(user) {
                    jqalert('Usuario ' + user.name + ' asignado a la pantalla ' + $scope.screen.name + ' correctamente');
                    $scope.model.assigned_users = ScreenUsersService.getAssignedUsers({screen: code});
                    $scope.model.users = ScreenUsersService.getUsers({screen: code}, function() {
                        $('.select2').select2();
                    });
                }, function(err) {
                    handleError(err.status);
                });
            }
        };

        $scope.visible = function(email_user) {
            if(confirm('¿Está seguro?')) {
                ScreenUsersService.visible({
                    screen: code,
                    user: email_user
                }, function(user) {
                    jqalert('Se ha marcado la pantalla ' + $scope.screen.name + ' visible para el usuario ' + user.name + ' correctamente');
                    $scope.model.assigned_users = ScreenUsersService.getAssignedUsers({screen: code});
                }, function(err) {
                    handleError(err.status);
                });
            }
        };

        $scope.invisible = function(email_user) {
            if(confirm('¿Está seguro?')) {
                ScreenUsersService.invisible({
                    screen: code,
                    user: email_user
                }, function(user) {
                    jqalert('Se ha marcado la pantalla ' + screen.name + ' no visible para el usuario ' + user.name + ' correctamente');
                    $scope.model.assigned_users = ScreenUsersService.getAssignedUsers({screen: code});
                }, function(err) {
                    handleError(err.status);
                });
            }
        };

        function error(field, msg) {
            $scope.model[field] = msg;
        }

        function handleError(code) {
            switch (code) {
                case 400: return alert('La petición es incorrecta. Falta algún parámetro obligatorio.');
                case 403: return alert('Credenciales de acceso incorrectas. Cierre sesión y vuelva a acceder con su email y contraseña.');
                case 404: return alert('El usuario no existe.');
                case 405: return alert('El método HTTP no está soportado.');
                case 409: return alert('El usuario ya existe.');
                case 500: return alert('Error interno. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
                default: return alert('Error desconocido. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
            }
        }

        function cleanErrors() {
            $scope.model.errorName = false;
        }

    }]);