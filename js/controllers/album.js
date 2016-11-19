angular.module('angularApp').controller('AlbumController', [
    '$scope',
    '$location',
    '$routeParams',
	'$timeout',
    'store',
    'md5',
    'TokenService',
    'AlbumService',
    'ImageService',
	'AlbumImageService',
    'ScreenService',
    'ModuleService',
    'PermissionsService',
    'IsAdminService',
    'TOKEN',
    'ACCESS_POINT',
    function($scope, $location, $routeParams, $timeout, store, md5, TokenService, AlbumService, ImageService, AlbumImageService, ScreenService, ModuleService, PermissionsService, IsAdminService, TOKEN, ACCESS_POINT) {

        $scope.model = {
            albums: [],
            errorName: false,
            name: ''
        };

        $scope.album = null;
        $scope.image = null;
        $scope.screens = null;

        var id = $routeParams.id || undefined;
        var image = $routeParams.image || undefined;

        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            $scope.model.albums = AlbumService.query();
            if (id) {
                $scope.album = AlbumService.get({id: id}, function(){
                    $scope.model.name = $scope.album.name;
                });
                $scope.screens = ScreenService.query();
            }
            if (image) {
                $scope.image = ImageService.get({id: image});
            }
        });

        var permissions = PermissionsService.query();
        $scope.permission = function(which) {
            if (permissions && permissions.length > 0) {
                return permissions[0][which] === '1';
            }
            return false;
        };

        $scope.canEditAlbum = function () {
            if(IsAdminService.isAdmin()) {
                return $scope.album ? $scope.album.is_mine === '1' : false;
            }
            else if($scope.album) {
                return $scope.album.is_mine === '1' && $scope.permission('albums');
            }
            else return false;
        };

        $scope.canUploadImages = function() {
            if(IsAdminService.isAdmin()) {
                return $scope.permission('upload_images')
            }
            else if($scope.album) {
                return $scope.album.is_mine === '1' && $scope.permission('upload_images');
            }
            else return false;
        };

        $scope.isAdmin = function() {
            return IsAdminService.isAdmin();
        };

		$scope.setImage = function(image) {
            $scope.image = image;
        };

		$scope.setPos = function(pos) {
            $scope.pos = pos;
        };

        $scope.submit = function() {
            cleanErrors();
            var name = $scope.model.name;
            if (!name.trim()) return error('errorName', 'introduce un nome para o \u00E1lbum');
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                AlbumService.save({name: name}, function(album) {
                    jqalert('\u00E1lbum ' + album.name + ' creado correctamente');
                    $location.path('album');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.update = function() {
            cleanErrors();
            var name = $scope.album.name;
            if (!name.trim()) return error('errorName', 'introduce un nome para o \u00E1lbum');
            var id = $scope.album.id;
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                AlbumService.update({id: id, name: name}, function(album) {
                    jqalert('\u00E1lbum ' + album.name + ' actualizado correctamente');
                    $location.path('album/' + album.id);
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
                AlbumService.remove({id: id}, function() {
                    jqalert('\u00E1lbum ' + $scope.album.name + ' eliminado correctamente');
                    $location.path('album');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };
		$scope.removeFrAl = function(image) {
			$('#myModalLabel').modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
			$('#myModaldesasoc').modal('show');
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                    AlbumImageService.remove({album: id, image: image}, function() {
                        jqalert('imaxe eliminada do \u00E1lbum');
                        $timeout(function() {
							$('#iralbum').click();
						},400)
                    }, function(err) {
                        handleError(err.status);
                    });
            });
        };
        $scope.send = function(screen) {
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                ModuleService.save({album: id, screen: screen.code}, function() {
                    jqalert('\u00E1lbum ' + $scope.album.name + ' enviado correctamente \u00E1 pantalla ' + screen.name);
                    $location.path('album/' + id);
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.sendToAll = function() {
            $scope.screens.forEach(function(screen) {
                TokenService.get(function(json) {
                    store.set(TOKEN, json.token);
                    ModuleService.save({album: id, screen: screen.code}, function() {
                        jqalert('�\u00E1lbum ' + $scope.album.name + ' enviado correctamente \u00E1 pantalla ' + screen.name);
                        $location.path('album/' + id);
                    }, function(err) {
                        handleError(err.status);
                    });
                });
            });
        };

        function error(field, msg) {
            $scope.model[field] = msg;
        }
		function getImageAlbumId(i) {
	var user = store.get('wayhoy_user');
    var pass = store.get('wayhoy_pass');
    var hash = cryptofoo.hash('md5', user + pass );
    var addUrl = ACCESS_POINT+'/mobile/luk/imageaddalbum.php?u=' + encodeURIComponent(user) + '&t=' + pass +'&image='+i;
	$.ajax({
		url : addUrl,
		type : 'get',
		success : function(data) {	
		return data = data.trim();		
			
		},
		error : function(data) {
			jqalert('erro descoñecido, proba en uns minutos');
		}
	})
	}
	//$scope.album.images; //Aquí están listadas todas las imágenes del album, ya está definido de antes.
    	$scope.obtenerPosicionesDisponibles = function() {
        	var result = [];
        	var contador = 1;
			if (!$scope.album || !$scope.album.images) return result;
        	$scope.album.images.forEach(function() {
            	result.push(contador);
            	contador++;
          });
        	return result;
      };
	  $scope.cambiarPosicion = function(image, a) {           
		 var posicion = $scope.model.position;
			if (!posicion){
				jqalert('esta xa é a posición actual!');
				return false;
				}
				TokenService.get(function(json) {
                   store.set(TOKEN, json.token);
				});
				ImageService.update({orden: posicion, id: image, album: a});
				jqalert('posición da imaxe cambiada');	
					$timeout(function() {
							$('#iralbum').click();
						},400)
					$('.close').click();
            		$('body').removeClass('modal-open');
            		$('.modal-backdrop').remove();				
      	};
		$scope.addimagen = function(i,a,r,d) {
			var user = store.get('wayhoy_user');
			var pass = store.get('wayhoy_pass');
			var hash = cryptofoo.hash('md5', user + pass );
			var joiner='';
			if(d){
				getImageAlbumId(i)
				}
			if (r){
				joiner = '&galR=si';
				}
			var addUrl = ACCESS_POINT+'/mobile/luk/imageaddalbum.php?u=' + encodeURIComponent(user) + '&t=' + pass +'&image='+i+'&album='+a+joiner;
			$.ajax({
				url : addUrl,
				type : 'get',
				success : function(data) {
					data = data.trim();
					$location.path('album/' + a + '/image/' + data);
					$scope.$apply();
				},
				error : function(data) {
					jqalert('erro descoñecido, proba en uns minutos');
				}
			})
		}
        function handleError(code) {
            switch (code) {
                case 400: return jqalert('petición incorrecta. Falta algún par\u00E1metro obrigatorio.');
                case 403: return jqalert('credenciales de acceso incorrectas. Peche sesión e volva a acceder co seu email e contrasinal.');
                case 404: return jqalert('\u00E1lbum o la pantalla no existen.');
                case 405: return jqalert('método non soportado.');
                case 500: return jqalert('Erro interno. Estamos a traballar para solucionalo. Inténteo de novo pasados uns minutos.');
                default: return jqalert('Erro desconocido. Estamos a traballar para solucionalo.Inténteo de novo pasados uns minutos.');
            }
        }

        function cleanErrors() {
            $scope.model.errorName = false;
        }
    }
]);