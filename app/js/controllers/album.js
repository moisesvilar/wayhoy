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
    'TOKEN',
    'ACCESS_POINT',
    function($scope, $location, $routeParams, $timeout, store, md5, TokenService, AlbumService, ImageService, AlbumImageService, ScreenService, ModuleService, TOKEN, ACCESS_POINT) {
        
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
		var albumId  = $routeParams.album || undefined;
        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            $scope.model.albums = AlbumService.query();
            if (id) {
                $scope.album = AlbumService.get({id: id}, function(){
                    $scope.model.name = $scope.album.name;
				/*	console.log($scope.album);
					var i=0;
					$scope.model.images = $scope.album.images.forEach(function() {
            				$scope.image = ImageService.get({id: $scope.album.images[i].idim});
							console.log($scope.image);						
							i++;
          			});
					*/
                });
                $scope.screens = ScreenService.query();
            }
            if (image) {
                $scope.image = ImageService.get({id: image});
            }
			
        });
		$scope.loadMore = function(){
			alert();
			}
		$scope.setImage = function(image) {
            $scope.image = image;
        };
		$scope.setPos = function(pos) {
            $scope.pos = pos;
        };
        $scope.submit = function() {
			console.log('submit');
            cleanErrors();
            var name = $scope.model.name;
            if (!name.trim()) return error('errorName', 'introduce un nombre para elo \u00E1lbum');
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
            if (!name.trim()) return error('errorName', 'introduce un nombre para el \u00E1lbum');
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
                        jqalert('imagene eliminada do \u00E1lbum');
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
                ModuleService.save({album: id, screen: screen.code, cambio: sessionStorage.getItem('cambio'), valor: sessionStorage.getItem('valor')}, function() {
					var tx ='Item enviado correctamente, pantalla: ' + screen.name;
					if (sessionStorage.getItem('cambio') == 'radio'){
						tx='canal asignado a la plantilla full en la pantalla: ' + screen.name;
					}
                    jqalert(tx);
                    $location.path('album');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };
        $scope.sendToAll = function() {
            $scope.screens.forEach(function(screen) {				
                TokenService.get(function(json) {
                    store.set(TOKEN, json.token);
                    ModuleService.save({album: id, screen: screen.code, cambio: sessionStorage.getItem('cambio'), valor: sessionStorage.getItem('valor')}, function() {
                        jqalert('Item enviado correctamente, pantalla: ' + screen.name);
						console.log(sessionStorage.getItem('cambio'),sessionStorage.getItem('valor'));
                        $location.path('album');
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
			var pass = sessionStorage.getItem('wayhoy_pass');
			var hash = cryptofoo.hash('md5', user + pass );
			var addUrl = ACCESS_POINT+'/mobile/luk/imageaddalbum.php?u=' + encodeURIComponent(user) + '&t=' + hash +'&image='+i;
			$.ajax({
				url : addUrl,
				type : 'get',
				success : function(data) {	
					return data = data.trim();							
				},
				error : function(data) {
					jqalert('error, puede que no tengas conexión');
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
				jqalert('esta ya es la posición actual!');
				return false;
				}
				TokenService.get(function(json) {
                   store.set(TOKEN, json.token);
				});
				ImageService.update({orden: posicion, id: image, album: a});
				jqalert('posición cambiada');	
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
			var addUrl = ACCESS_POINT+'/mobile/luk/imageaddalbum.php?u=' + encodeURIComponent(user) + '&t=' + hash +'&image='+i+'&album='+a+joiner;
			console.log(addUrl);
			$.ajax({
				url : addUrl,
				type : 'get',
				success : function(data) {
					data = data.trim();
					$location.path('album/' + a + '/image/' + data);
					$scope.$apply();
				},
				error : function(data) {
					jqalert('erro, sin conexion?');
				}
			})
		}
			$scope.addfb = function(name, alb) {
			var user = store.get('wayhoy_user');
			var pass = store.get('wayhoy_pass');
			var hash = cryptofoo.hash('md5', user + pass );
			var addUrl = ACCESS_POINT+'/mobile/luk/addfb.php?u=' + encodeURIComponent(user) + '&t=' + hash +'&album='+albumId+'&fanpage_name='+name+'&album_name='+alb;
			$.ajax({
				url : addUrl,
				type : 'GET',
				success : function(data) {
					data = data.trim();
					$location.path('album/' + id + '/image/' + data);
					$scope.$apply();
				},
				error : function(data) {
					jqalert('erro, sin conexion?');
				}
			})
		}
        function handleError(code) {
            switch (code) {
                case 400: return jqalert('petición incorrecta. Falta algún par\u00E1metro obligatorio.');
                case 403: return jqalert('credenciales de acceso incorrectas');
                case 404: return jqalert('\u00E1lbum o la pantalla no existen.');
                case 405: return jqalert('método no soportado.');
                case 500: return jqalert('Error interno.');
                default: return jqalert('Error desconocido. Sin conexión?');
            }
        }

        function cleanErrors() {
            $scope.model.errorName = false;
        }
    }
]);