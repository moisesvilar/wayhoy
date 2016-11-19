angular.module('angularApp').controller('GalleryController', [
    '$scope',
    '$location',
    '$routeParams',
    'store',
    'md5',
    'TokenService',
    'ImageService',
    'PermissionsService',
    'USER',
    'PASSWORD',
    'TOKEN',
    function($scope, $location, $routeParams, store, md5, TokenService, ImageService, PermissionsService, USER, PASSWORD, TOKEN) {

        $scope.model = {
            images: []
        };

        var permissions = PermissionsService.query();
        $scope.permission = function(which) {
            if (permissions && permissions.length > 0) {
                return permissions[0][which] === '1';
            }
            return false;
        };

        $scope.image = null;

        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            var route = $routeParams.si;
			if (!route){
            $scope.model.images = ImageService.query();
			}else{
			$scope.model.images = ImageService.galleryr();
			}
        });

        $scope.setImage = function(image) {
            $scope.image = image;
        };
		
        $scope.remove = function() {
			$('#cancelabut').click();
            if ($scope.image) {
                TokenService.get(function(json) {
                    store.set(TOKEN, json.token);
                    ImageService.remove({id: $scope.image.id}, function() {
                        jqalert('imaxe eliminada correctamente');
                        $scope.model.images = ImageService.query();
                        $scope.image = null;
                    }, function(err) {
                        handleError(err.status);
                    });
                });
            }
        };

        function handleError(code) {
            switch (code) {
                case 400: return jqalert('La petición es incorrecta. Falta algún parámetro obligatorio.');
                case 403: return jqalert('Credenciales de acceso incorrectas. Cierre sesión y vuelva a acceder con su email y contraseña.');
                case 404: return jqalert('El album o la imagen no existen.');
                case 405: return jqalert('El método HTTP no está soportado.');
                case 500: return jqalert('Error interno. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
                default: return jqalert('Error desconocido. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
            }
        }
    }
]);