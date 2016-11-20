angular.module('angularApp').controller('UsersController', [
    '$scope',
    '$routeParams',
    '$location',
    'TokenService',
    'UsersService',
    'IsAdminService',
    'TOKEN',
    'USER',
    function($scope, $routeParams, $location, TokenService, UsersService, IsAdminService, TOKEN, USER) {

        if(!IsAdminService.isAdmin()) {
            $location.path('');
        }

        $scope.model = {
            users: [],
            name: '',
            email: '',
            nif: '',
            password: ''
        };

        $scope.user = null;

        var email = $routeParams.email || undefined;

        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            $scope.model.users = UsersService.query();
            if (email) {
                var user = store.get(USER);
                $scope.user = UsersService.get({id: email}, function(){
                    $scope.model.name = $scope.user.name;
                    $scope.model.email = $scope.user.email;
                    $scope.model.nif = $scope.user.nif;
                });
            }
        });

        $scope.togglePermission = function(selectedPermission) {
            $scope.user.permissions.forEach(function(permission) {
                if (permission.id == selectedPermission.id) {
                    permission.value = permission.value === '0' ? '1' : '0';
                    var $button = $('#'+ permission.id);
                    if (permission.selected) {
                        $button.removeClass('btn-default');
                        $button.addClass('btn-danger');
                    }
                    else {
                        $button.removeClass('btn-danger');
                        $button.addClass('btn-default');
                    }
                }
            });
        };

        $scope.submit = function() {
            cleanErrors();
            var name = $scope.model.name;
            var nif = $scope.model.nif;
            var email = $scope.model.email;
            var password = $scope.model.password;
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                UsersService.save({email: email, name: name, nif: nif, password: password}, function(user) {
                    jqalert('Usuario ' + user.name + ' creado correctamente');
                    $location.path('users');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.update = function() {
            cleanErrors();
            var name = $scope.model.name;
            var nif = $scope.model.nif;
            var email = $scope.model.email;
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                UsersService.update({email: email, name: name, nif: nif, permissions: $scope.user.permissions}, function(user) {
                    jqalert('Usuario ' + user.name + ' actualizado correctamente');
                    $location.path('users');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.remove = function (){
            if(!email) return;
            if(confirm('¿Está seguro?')) {
                TokenService.get(function(json) {
                    store.set(TOKEN, json.token);
                    UsersService.remove({email: email}, function() {
                        jqalert('Usuario ' + $scope.user.name + ' eliminado correctamente');
                        $location.path('users');
                    }, function(err) {
                        handleError(err.status);
                    });
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