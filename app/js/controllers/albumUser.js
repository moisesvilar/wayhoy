angular.module('angularApp').controller('AlbumUsersController', [
    '$scope',
    '$routeParams',
    '$location',
    'TokenService',
    'AlbumUsersService',
    'IsAdminService',
    'AlbumService',
    function($scope, $routeParams, $location, TokenService, AlbumUsersService, IsAdminService, AlbumService) {

        if(!IsAdminService.isAdmin()) {
            $location.path('');
        }

        $scope.model = {
            users: [],
            assigned_users: [],
            email_user: null
        };

        $scope.album = null;

        var album_id = $routeParams.album || undefined;
        $scope.model.users = AlbumUsersService.getUsers({album: album_id}, function() {
            $('.select2').select2();
        });
        $scope.album = AlbumService.get({id: album_id});
        $scope.model.assigned_users = AlbumUsersService.getAssignedUsers({album: album_id});

        $scope.assign = function() {
            AlbumUsersService.assign({
                album: album_id,
                user: $scope.model.email_user
            }, function(user) {
                jqalert('Usuario ' + user.name + ' asignado al album ' + $scope.album.name + ' correctamente');
                $scope.model.assigned_users = AlbumUsersService.getAssignedUsers({album: album_id});
            }, function(err) {
                handleError(err.status);
            });
        };

        $scope.unassing = function(email_user) {
            if(confirm('¿Está seguro?')) {
                AlbumUsersService.unassign({
                    album: album_id,
                    user: email_user
                }, function(user) {
                    jqalert('Usuario ' + user.name + ' asignado al album ' + $scope.album.name + ' correctamente');
                    $scope.model.assigned_users = AlbumUsersService.getAssignedUsers({album: album_id});
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