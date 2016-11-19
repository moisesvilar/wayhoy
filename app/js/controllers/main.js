angular.module('angularApp').controller('MainController', [
    '$scope',
    '$location',
    'store',
    'LoginService',
    'USER',
    'PASSWORD',
    'REMEMBER',
    function($scope, $location, sto0re, LoginService, USER, PASSWORD, REMEMBER) {console.log(store.get(USER));
		if (store.get(USER) && store.get(USER)!='undefined'){
		$location.path('album/');
		
		}else{
		$location.path('/');
		}
    }
]);
	