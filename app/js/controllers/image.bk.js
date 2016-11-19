angular.module('angularApp').controller('ImageController', [
    '$scope',
    '$location',
    '$routeParams',
    'store',
    'md5',
    'TokenService',
    'ImageService',
    'AlbumService',
    'AlbumImageService',
    'USER',
    'PASSWORD',
    'TOKEN',
    function($scope, $location, $routeParams, store, md5, TokenService, ImageService, AlbumService, AlbumImageService, USER, PASSWORD, TOKEN) {

        $(":file").filestyle({
            icon: false,
            buttonText: 'selecciona unha imaxe'
        });

        $scope.model = {
            flow: undefined,
            title: '',
            description: '',
            subdescription: ''
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
            if (imageId && albumId) {
                $scope.image = ImageService.get({id: imageId}, function() {
                    var albumImage = AlbumImageService.get({album: albumId, image: imageId}, function() {
                        $scope.model.title = albumImage.title;
                        $scope.model.descrition = albumImage.description;
                        $scope.model.subdescrition = albumImage.subdescription;
                    });
                });
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

        $scope.configFlow = {
            headers: function(flowFile, flowChunk) {
                var token = store.get(TOKEN);
                var user = store.get(USER);
                var pass = store.get(PASSWORD);
                return {
                    User: user,
                    Secret:  md5.createHash(user + pass + token)
                };
            },
            target: UploadUrl,
            maxChunkRetries: 1,
            chunkRetryInterval: 5000,
            simultaneousUploads: 1,
            singleFile: true,
            testChunks:false
        };

        $scope.uploadImage = function() {
            TokenService.get(function(json) {
                $scope.model.flow.upload();
            });
        };

        $scope.uploadComplete = function(flow, file, message) {
            try {
                var json = JSON.parse(message);
            }
            catch(e) {
                return alert('ocurríu un erro ó subir a imaxe');
            }
            jqalert('imaxe engadida correctamente!');
            var id = json.id;
            $scope.model.flow.files = [];
            if (albumId) {
                $location.path('/album/' + albumId + '/image/' + id);
            }
            else {
                $location.path('/gallery');
            }
        };

        $scope.submit = function() {
            var title = $scope.model.title;
            var description = $scope.model.descrition;
            var subdescription = $scope.model.subdescrition;
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                AlbumImageService.update({
                    album: albumId,
                    id: imageId,
                    title: title,
                    description: description,
                    subdescription: subdescription
                }, function(album) {
                    jqalert('datos gardados correctamente');
                    $location.path('album/' + albumId);
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.remove = function() {
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

        function handleError(code) {
            switch (code) {
                case 400: return alert('petición incorrecta. Falta algún parámetro obrigatorio.');
                case 403: return alert('Credenciales de acceso incorrectas. Peche sesión y volva a acceder có seu email e contrasinal.');
                case 404: return alert('o album ou pantalla non existen.');
                case 405: return alert('método non soportado.');
                case 500: return alert('Erro interno. Estamos a traballar para solucionalo. Inténtao de novo pasados uns minutos.');
                default: return alert('Erro desconocido. Estamos a traballar para solucionalo.Intérnteo de novo pasados un¡s minutos.');
            }
        }
    }
]);