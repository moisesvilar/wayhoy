angular.module('angularApp').controller('ProgramController', [
    '$scope',
    '$location',
    '$routeParams',
    'store',
    'md5',
    'TokenService',
    'ScreenService',
	'AlbumService',
	'ProgrammingService',
    'USER',
    'TOKEN',
    function($scope, $location, $routeParams, store, md5, TokenService, ScreenService, AlbumService, ProgrammingService, USER, TOKEN) {
        //store.set('album',1000);
		var dayId = $routeParams.day || undefined;
        var programmingId = $routeParams.id || undefined;
		$scope.addTime = function() {
			$scope.model.times.push({
				start: null,
				end: null
			});
		};
		
		$scope.removeTime = function() {
			$scope.model.times.pop();
		};
	
		$scope.model = {};
        $scope.model.screen = null;
		$scope.model.album = null;
		$scope.model.cambio = null;
		$scope.model.valor = null;
		$scope.model.days = [
			{
				id: 2,
				name: 'Lunes'
			},
			{
				id: 3,
				name: 'Martes'
			},
			{
				id: 4,
				name: 'Miércoles'
			},
			{
				id: 5,
				name: 'Jueves'
			},
			{
				id: 6,
				name: 'Viernes'
			},
			{
				id: 7,
				name: 'Sábado'
			},
			{
				id: 1,
				name: 'Domingo'
			}
		];
		$scope.model.times = [];
		$scope.addTime();
        $scope.model.intervals = [
            '00:00',
            '00:30',
            '01:00',
            '01:30',
            '02:00',
            '02:30',
            '03:00',
            '03:30',
            '04:00',
            '04:30',
            '05:00',
            '05:30',
            '06:00',
            '06:30',
            '07:00',
            '07:30',
            '08:00',
            '08:30',
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '12:00',
            '12:30',
            '13:00',
            '13:30',
            '14:00',
            '14:30',
            '15:00',
            '15:30',
            '16:00',
            '16:30',
            '17:00',
            '17:30',
            '18:00',
            '18:30',
            '19:00',
            '19:30',
            '20:00',
            '20:30',
            '21:00',
            '21:30',
            '22:00',
            '22:30',
            '23:00',
            '23:30'
        ];
        TokenService.get(function(json) {
            store.set(TOKEN, json.token);
            $scope.model.screens = ScreenService.query();
            $scope.model.albums = AlbumService.query();
            $scope.model.programmings = ProgrammingService.query(function() {
                if (programmingId) {
                    $scope.model.programmings.forEach(function(item) {
                       if (item.programming_id == programmingId) {
                           $scope.model.programming = item;
                           $scope.model.album = AlbumService.get({id: item.album});
                           $scope.model.screen = ScreenService.get({code: item.screen});
						   $scope.model.cambio = item.cambio;
						   $scope.model.valor = item.valor;
						   $scope.model.set = item.valor;
                           $scope.model.time = {
                               start: item.start,
                               end: item.end
                           };
                           $scope.model.times = [$scope.model.time];
                       }
                    });
                }else{
            				$scope.model.album = AlbumService.get({id: sessionStorage.getItem('album')});
							$scope.model.cambio = sessionStorage.getItem('cambio');
							$scope.model.valor = sessionStorage.getItem('valor'); 
							$scope.model.set = sessionStorage.getItem('set');
					}
            });
        });
        if (dayId) {
            $scope.model.days.forEach(function(day) {
                if (day.id == dayId) {
                    $scope.model.day = day;
                    day.selected = true;
                }
            });
        }

        $scope.toggleDay = function(selectedDay) {
            $scope.model.days.forEach(function(day) {
                if (day.id == selectedDay.id) {
                    day.selected = !day.selected;
                    var $button = $('#'+ day.id);
                    if (day.selected) {
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

		$scope.update = function() {
			if (!$scope.model.screen) return jqalert('Selecciona una pantalla');
			var error=false;
			$scope.model.times.forEach(function(time) {
				if (time.end <= time.start) {
                    jqalert('Finalizaión anterior al inicio?');
					error = true;
				}
			});
            if (error) return;
			if (!$scope.model.album) return jqalert('Selecciona un álbum!');

			var peticion = {
                id: $scope.model.programming ? $scope.model.programming.programming_id : null,
				screen: $scope.model.screen.code ? $scope.model.screen.code : $scope.model.screen,
				album: $scope.model.album.id ? $scope.model.album.id : $scope.model.album,
				cambio: sessionStorage.getItem('cambio'),
				valor: sessionStorage.getItem('valor'),
				days: $scope.model.days.filter(function(day) {
                    return day.selected == true;
				}),
				times: $scope.model.times
			};
            if (peticion.days.length == 0) return jqalert('Selecciona un día da semana!');

			TokenService.get(function(json) {
				store.set(TOKEN, json.token);
                if ($scope.model.programming) {
                    ProgrammingService.update(peticion, function(result) {
						if (!result.msg){result.msg='Modificado correctamente';}
                        jqalert(result.msg);
                        if (dayId) {
                            $location.path('programming/' + dayId );
                        }
                        else {
                            $location.path('programming');
                        }
                    });
                }
                else {
                    ProgrammingService.save(peticion, function(result) {
						if (!result.msg){result.msg='Modificado correctamente';}
                        jqalert(result.msg);
						
                        if (dayId) {
                            $location.path('programming/' + dayId );
                        }
                        else {
                            $location.path('programming');
                        }
                    });
                }
			});
		};

        $scope.remove = function(id) {
			$('#your-modal-id').modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
			
            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                ProgrammingService.remove({id: id}, function() {
                    jqalert('Programación eliminada correctamente');
                    if (dayId) {
                        $location.path('programming/' + dayId );
                    }
                    else {
                        $location.path('programming');
                    }
                }, function(err) {
                    handleError(err.status);
                });
            });
        };

        $scope.countProgrammings = function(dayId) {
            var count = 0;
            if (!$scope.model.programmings) return 0;
            $scope.model.programmings.forEach(function(programming) {
                if (programming.day == dayId) count++;
            });
            return count;
        };

        $scope.getProgrammings = function(dayId) {
            if (!$scope.model.programmings) return [];
            return $scope.model.programmings.filter(function(item) {
                return (item.day == dayId);
            });
        };

        $scope.getAlbum = function(albumId) {
            var album = null;
            $scope.model.albums.forEach(function(item) {
                if (item.id == albumId) album = item;
            });
            return album;
        };

        $scope.getScreen = function(screenId) {
            var screen = null;
            $scope.model.screens.forEach(function(item) {
                if (item.code == screenId) screen = item;
            });
            return screen;
        };

        function handleError(code) {
            switch (code) {
                case 400: return jqalert('petición incorrecta. Falta algún par\u00E1metro obligatorio.');
                case 403: return jqalert('credenciales de acceso incorrectas');
                case 404: return jqalert('programación non existe.');
                case 405: return jqalert('método no soportado.');
                case 500: return jqalert('Error interno');
                default: return jqalert('Error desconocido. Sin conexión?');
            }
        }
    }
]);