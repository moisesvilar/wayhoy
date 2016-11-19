angular.module('angularApp').controller('ScreenController', [
    '$scope',
    '$location',
    '$routeParams',
    'store',
    'md5',
    'TokenService',
    'ScreenService',
    'USER',
    'TOKEN',
    function($scope, $location, $routeParams, store, md5, TokenService, ScreenService, USER, TOKEN) {

        $scope.model = {
            screens: [],
            code: '',
            name: ''
        };

        $scope.screen = null;

        var code = $routeParams.code || undefined;

        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            $scope.model.screens = ScreenService.query();
            if (code) {
				cleanErrors();
                var user = store.get(USER);
                $scope.screen = ScreenService.get({code: code}, function(){
                    $scope.model.code = $scope.screen.code;
                    $scope.model.name = $scope.screen.name;
					var input_date = new Date($scope.screen.updated).getTime();
					var curr_date = new Date().getTime();
					var dif = curr_date - input_date;
						if(dif > 3000 || isNaN(dif)){
  							$scope.conectada = false;    
						}else{
  							$scope.conectada = 'si'; 
						}
					$scope.preview = '?ICC='+$scope.screen.icc +'&client='+$scope.screen.user_id_r;
                });
            }
        });
		$scope.cierraPrev=function() {
		$location.path('screen');	
		$('#myModalLabel').modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();	
		}
        $scope.submit = function() {
            cleanErrors();
            var name = $scope.model.name;
            var code = $scope.model.code;
            if (!code.trim()) return error('errorCode', 'Debes introducir el código que se muestra en la pantalla');
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                ScreenService.save({code: code, name: name}, function(screen) {
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
                ScreenService.update({code: code, name: name}, function(screen) {
                    jqalert('Pantalla ' + screen.name + ' (' + screen.code + ') actualizada correctamente');
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
    }
]);