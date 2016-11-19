angular.module('angularApp').controller('ImageController', [
    '$scope',
    '$location',
    '$routeParams',
    '$http',
    'store',
    'md5',
    'TokenService',
    'ImageService',
    'AlbumService',
    'AlbumImageService',
    'USER',
    'PASSWORD',
    'TOKEN',
	'ACCESS_POINT',
    function($scope, $location, $routeParams, $http, store, md5, TokenService, ImageService, AlbumService, AlbumImageService, USER, PASSWORD, TOKEN, ACCESS_POINT) {

        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            $scope.model.images = ImageService.query();
        });

        $(":file").filestyle({
            icon: false,
            buttonText: 'subir imagen'
        });

        $scope.model = {
            flow: undefined,
            title: '',
            description: '',
            subdescription: '',
			id: '',
			duration: '',
            file: undefined,
			scale: '',
			angle : '',
			x : '',
			y : '',
			url : '',
			submitted: false			
        };
		
        $scope.album = null;
        $scope.image = null;
        $scope.gallery = {
            images: []
        };
		$scope.galleryr = {
            images: []
        };
        var albumId = $routeParams.album || undefined;
        var imageId = $routeParams.image || undefined;
		
        TokenService.get(function(json) {
            store.set(TOKEN, json.token);  
			if (albumId) {        
                $scope.gallery.images = ImageService.query();
				$scope.galleryr.images = ImageService.galleryr();
                $scope.album = AlbumService.get({id: albumId}); 
				store.set('album', albumId);
				}
    			$scope.image = ImageService.albumimage({id: imageId}, function() {
       	 		if (!$scope.image.title || $scope.image.title=='undefined' || $scope.image.title == 'null'){ $scope.image.title=''; }
        		if (!$scope.image.description || $scope.image.description=='undefined' || $scope.image.description == 'null'){ $scope.image.description=''; }
        		if (!$scope.image.subdescription || $scope.image.subdescription=='undefined' || $scope.image.subdescription == 'null'){ $scope.image.subdescription=''; }
				if ($scope.image.duration == 0 || !$scope.image.duration || $scope.image.duration==''){
					$scope.model.duration = '';
					}else{
					$scope.model.duration = $scope.image.duration;
					if (!$scope.image.duration||$scope.image.duration=='undefined'){
					$scope.model.duration = '';	
						}
				}
				$scope.model.title = $scope.image.title;
        		$scope.model.description = $scope.image.description;
        		$scope.model.subdescription = $scope.image.subdescription;
    				});		
			if (imageId){
			$scope.model.albums = AlbumService.query();
                    $scope.model.name = $scope.album.name;
					$scope.model.id = $scope.album.id;
				}
        	});

        var UploadUrl = ImageService.Url().replace(':id', '');
        if (albumId || imageId) {
            UploadUrl = AlbumImageService.Url();
            if (albumId) {
                UploadUrl = UploadUrl.replace('/:album', '/' + albumId);
            }
            else {
                UploadUrl = UploadUrl.replace('/:album', '');
            }
            if (imageId) {
                UploadUrl = UploadUrl.replace('/:image', '/' + imageId);
            }
            else {
                UploadUrl = UploadUrl.replace('/:image', '');
            }
        }

        $scope.uploadImage = function() {
            var file = $scope.model.file;
            if (!file) {
                return alert('debes seleccionar una imagen');
            }
            TokenService.get(function(json) {
                var fd = new FormData();
                fd.append('file', file);
                $http.post(UploadUrl, fd, {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined}
                }).success(function(json) {
                    jqalert('imaxe Subida');
					$scope.model.submitted = true;
					$scope.image = json;
					imageId = json.id;
					if (json.url!=json.url.replace('.mp4', '.jpg')){
						$location.path('album/' + albumId);
						$scope.$apply();
						}
                }).error(function(data, status) {
                    handleError(status);
                });
            });
        };
		
        $scope.submit = function() {
			console.log('submit');
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
					if (!$scope.model.title || $scope.model.title=='undefined'){
						var titulo='';
					}else{
						var titulo=$scope.model.title;
					}
					if (!$scope.model.description|| $scope.model.description=='undefined'){
						var descripcion='';
					}else{
						var descripcion=$scope.model.description;
					}
					if (!$scope.model.subdescription|| $scope.model.subdescription=='undefined'){
						var subdescripcion='';
					}else{
						var subdescripcion=$scope.model.subdescription;
					}
						
				var data = {
                    album: albumId,
                    id: imageId,
                    title: titulo,
                    description: descripcion,
                    subdescription:subdescripcion,
					duration : $scope.model.duration,
					scale: $('#scale').val(),
					angle: $('#angle').val(),
					x: $('#x').val(),
					y: $('#y').val()
                };
                AlbumImageService.update(data, function(album) {
                    jqalert('datos gardados correctamente');
                    $location.path('album/' + albumId);
                }, function(err) {
                    handleError(err.status);
                });
            });
        };
		
		$scope.updateImage = function() {
            var title = $scope.model.title;
            var description = $scope.model.description;
            var subdescription = $scope.model.subdescription;
			var duration = $scope.model.duration;
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
				var picture = $('#sample_picture');
				var data = picture.guillotine('getData');
				data.id = imageId.trim();
                ImageService.update(data, function() {
                    jqalert('datos gardados correctamente');
					if (albumId) {
						getImageAlbumId(imageId.trim());						
					}
					else {
						$location.path('/gallery');
					}
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
                if (albumId || imageId) {
                    AlbumImageService.remove({album: albumId, image: imageId}, function() {
                        jqalert('imaxe eliminada do \u00E1lbum');
                        $location.path('album/' + albumId);
                    }, function(err) {
                        handleError(err.status);
                    });
                }
            });
        };

        $scope.removeImage = function() {
			$('#your-modal-id').modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                ImageService.remove({id: imageId}, function() {
                    jqalert('imaxe eliminada correctamente');
                    $location.path('/gallery');
                }, function(err) {
                    handleError(err.status);
                });
            });
        };
		var albumIdLuk;
		$scope.duplica = function(i, t, d, s, dur) {
			if(!albumIdLuk){
				jqalert('Debes elixir un álbum');
				return false;
				}
		$scope.addimage(i, albumIdLuk, null, t, d, s, 'du', dur);
        }
		$scope.quealbum = function(a) {
			albumIdLuk=a;
        }
$scope.addimage = function(i,a, r, t, d, s, du, dur) {
	console.log(i, a, r, t, d, s, du);
	var user = store.get('wayhoy_user');
    var pass = store.get('wayhoy_pass');
    var hash = cryptofoo.hash('md5', user + pass );
	var joiner = '';
	var addparm='';
	if (t!=''){
		var ti = t;
		var de = d;
		var su = s;
		}else{
		var ti = "";
		var de = "";
		var su = "";
		}
	if (r){
		joiner = '&galR=si';
		}
	var dur = dur;
    var addUrl = ACCESS_POINT+'/mobile/luk/imageaddalbum.php?u=' + encodeURIComponent(user) + '&t=' + hash + '&image=' + i + '&album=' + a + joiner + '&title=' + ti + '&desc=' + de + '&subdesc=' + su + '&duration='+dur;
	console.log(addUrl);
	$.ajax({
		url : addUrl,
		type : 'get',
		success : function(data) {
			data = data.trim();
			if(!du){
				addparm='/image/' + data;
				}
			$location.path('album/' + a + addparm);
			$scope.$apply();
		},
		error : function(data) {
			jqalert('error ¿Sin conexión ?');
		}
	})
	}
	
	function getImageAlbumId(i) {
	var user = store.get('wayhoy_user');
    var pass = store.get('wayhoy_pass');
    var hash = cryptofoo.hash('md5', user + pass );
    var addUrl = ACCESS_POINT+'/mobile/luk/imageaddalbum.php?u=' + encodeURIComponent(user) + '&t=' + hash +'&image='+i;
	$.ajax({
		url : addUrl,
		type : 'get',
		success : function(data) {	
		// data = data.trim();		
		data=data.trim();
			$location.path('/album/' + albumId + '/image/' + data);
			$scope.$apply();
		},
		error : function(data) {
			jqalert('erro descoñecido, proba en uns minutos');
		}
	})
	}
	
        function handleError(code) {
            switch (code) {
                case 400: return jqalert('petición incorrecta. Falta algún parámetro obrigatorio.');
				case 401: return jqalert('¡¡ MÁXIMO 10 MB !!');
                case 403: return jqalert('Credenciales de acceso incorrectas. Peche sesión y volva a acceder có seu email e contrasinal.');
                case 404: return jqalert('o album ou pantalla non existen.');
                case 405: return jqalert('método non soportado.');
                case 500: return jqalert('Error interno. Inténtalo de otro modo.');
                default: return jqalert('Erro desconocido. Estamos a traballar para solucionalo.Intérnteo de novo pasados un¡s minutos.');
            }
        }
    }
]);