var app = angular.module('angularApp', [
    'ui.bootstrap',
    'ngRoute',
    'ngResource',
    'ngCookies',
    'angular-storage',
    'angular-md5',
    'flow',
    'ngLoadingSpinner',
    'ngCordova'
]);

app.config(['$routeProvider', '$httpProvider', '$locationProvider', function($routeProvider, $httpProvider, $locationProvider) {
    $routeProvider
        .when('/', {
           templateUrl: 'partials/home.html',
           controller: 'MainController'
        })
        .when('/register', {
            templateUrl: 'partials/register.html',
            controller: 'RegisterController'
        })
		 .when('/legal', {
            templateUrl: 'partials/legal.html'
        })
        .when('/gallery', {
            templateUrl: 'partials/gallery.html?t='+$.now()+'',
            controller: 'GalleryController'
        })
		.when('/galleryr/:si', {
            templateUrl: 'partials/galleryr.html?t='+$.now()+'',
            controller: 'GalleryController'
        })
        .when('/programming', {
            templateUrl: 'partials/programming.html',
            controller: 'ProgramController'
        })
		.when('/programming/new', {
            templateUrl: 'partials/edit-programming.html',
			controller: 'ProgramController'
        })
        .when('/programming/new/:day', {
            templateUrl: 'partials/edit-programming.html',
            controller: 'ProgramController'
        })
        .when('/programming/:day', {
            templateUrl: 'partials/day-programming.html',
            controller: 'ProgramController'
        })
        .when('/programming/:day/:id', {
            templateUrl: 'partials/edit-programming.html',
            controller: 'ProgramController'
        })
        .when('/gallery/upload', {
            templateUrl: 'partials/upload-image.html',
            controller: 'ImageController'
        })
        .when('/user', {
            templateUrl: 'partials/users.html?t='+$.now()+'',
            controller: 'UsersController'
        })
        .when('/users/new', {
            templateUrl: 'partials/new-user.html',
            controller: 'UsersController'
        })
        .when('/users/:email', {
            templateUrl: 'partials/edit-user.html',
            controller: 'UsersController'
        })
        .when('/album-users/:album', {
            templateUrl: 'partials/album-users.html?t='+$.now()+'',
            controller: 'AlbumUsersController'
        })
        .when('/screen', {
            templateUrl: 'partials/screen.html',
            controller: 'ScreenController'
        })
        .when('/screen/new', {
            templateUrl: 'partials/new-screen.html',
            controller: 'ScreenController'
        })
        .when('/screen/:code', {
            templateUrl: 'partials/edit-screen.html',
            controller: 'ScreenController'
        })
        .when('/screen-users/:screen', {
            templateUrl: 'partials/screen-users.html?t='+$.now()+'',
            controller: 'ScreenUsersController'
        })
        .when('/album', {
            templateUrl: 'partials/album.html',
            controller: 'AlbumController'
        })
        .when('/album/new', {
            templateUrl: 'partials/new-album.html',
            controller: 'AlbumController'
        })
        .when('/album/:id', {
            templateUrl: 'partials/album-details.html',
            controller: 'AlbumController'
        })
		.when('/albumup/:id', {
            templateUrl: 'partials/albumup.html',
            controller: 'ImageController'
        })
		.when('/albumup', {
            templateUrl: 'partials/albumup.html',
            controller: 'UpdImageController'
        })
        .when('/edit-album/:id', {
            templateUrl: 'partials/edit-album.html',
            controller: 'AlbumController'
        })
        .when('/album/:album/upload', {
            templateUrl: 'partials/upload-image.html?t='+$.now()+'',
            controller: 'ImageController'
        })
        .when('/album/:id/send', {
            templateUrl: 'partials/send-album.html',
            controller: 'AlbumController'
        })
        .when('/album/:album/image/:image', {
            templateUrl: 'partials/image.html',
            controller: 'ImageController'
        })
		.when('/album/:album/image/:image/duplica/', {
            templateUrl: 'partials/image-duplica.html',
            controller: 'ImageController'
        })
        .when('/profile', {
            templateUrl: 'partials/profile.html',
            controller: 'ProfileController'
        })
        .when('/image/:image/album', {
            templateUrl: 'partials/add-to-album.html',
            controller: 'AlbumController'
        })
        .when('/recovery', {
            templateUrl: 'partials/recovery.html',
            controller: 'RecoveryController'
        })
        .otherwise({
           redirectTo: '/'
        }
    );

    $locationProvider.html5Mode(true);

    $httpProvider.interceptors.push('TokenInterceptor');
	<!--no cache -->

	if (!$httpProvider.defaults.headers.get) {
        $httpProvider.defaults.headers.get = {};    
    }    

    // Answer edited to include suggestions from comments
    // because previous version of code introduced browser-related errors

    //disable IE ajax request caching
    $httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
    // extra
    $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
    $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
	
	
}]);

app.run(function($rootScope, $location) {
    $rootScope.location = $location;
});

app.constant('ACCESS_POINT', 'https://wayhoy.com/wayhoy');
app.constant('USER', 'wayhoy_user');
app.constant('PASSWORD', 'wayhoy_pass');
app.constant('TOKEN', 'wayhoy_token');
app.constant('REMEMBER', 'wayhoy_remember');