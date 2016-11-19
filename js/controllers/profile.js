angular.module('angularApp').controller('ProfileController', [
    '$scope',
    '$location',
    'md5',
    'store',
    'UserService',
    'TokenService',
    'TOKEN',
    'PASSWORD',
    function($scope, $location, md5, store, UserService, TokenService, TOKEN, PASSWORD) {

        $scope.model = {
            oldPassword: undefined,
            newPassword: undefined,
            repeatPassword: undefined,
            success: false
        };

        $scope.submit = function() {
            cleanMessages();
            var oldPassword = $scope.model.oldPassword;
            var newPassword = $scope.model.newPassword;
            var repeatPassword = $scope.model.repeatPassword;

            if (!oldPassword) return error('errorOldPassword', 'tes que introducir o antigo contrasinal');
            if (!newPassword) return error('errorNewPassword', 'o contrasinaldebe ter 6 caracteres como mínimo');
            if (!repeatPassword) return error('errorRepeatPassword', 'tes que confirmar ocontrasinal');
            if (newPassword != repeatPassword) return error('errorRepeatPassword', 'os contrasinais non coinciden');
            if (newPassword.length < 6) return error('errorNewPassword', 'o contrasinal debe ser de 6 caracteres como mínimo');

            TokenService.get(function(json) {
                store.set(TOKEN, json.token);
                store.set(PASSWORD, oldPassword);
                UserService.update({
                    password: newPassword
                }, function(data) {
                    store.set(PASSWORD, newPassword);
                    $scope.model.success = true;
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
                 case 400: return alert('petición incorrecta. Falta algún parámetro obrigatorio.');
                case 403: return alert('Credenciales de acceso incorrectas. Peche sesión y volva a acceder có seu email e contrasinal.');
                case 404: return alert('o usuario non existe.');
                case 405: return alert('método non soportado.');
                case 500: return alert('Erro interno. Estamos a traballar para solucionalo. Inténtao de novo pasados uns minutos.');
                default: return alert('Erro desconocido. Estamos a traballar para solucionalo.Intérnteo de novo pasados un¡s minutos.');
            }
        }

        function cleanMessages() {
            $scope.model.errorCode = $scope.model.errorEmail = $scope.model.errorPassword =
                $scope.model.errorRepeatEmail = $scope.model.errorRepeatPassword = false;
            $scope.model.success = false;
        }
    }
]);