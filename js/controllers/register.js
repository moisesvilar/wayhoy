angular.module('angularApp').controller('RegisterController', [
    '$scope',
    '$location',
    'md5',
    'store',
    'UserService',
    'USER',
    'PASSWORD',
    'REMEMBER',
    function($scope, $location, md5, store, UserService, USER, PASSWORD, REMEMBER) {

        $scope.model = {
            code: undefined,
            email: undefined,
            password: undefined,
            repeatEmail: undefined,
            repeatPassword: undefined
        };

        $scope.submit = function() {
            cleanErrors();
            var code = $scope.model.code;
            var email = $scope.model.email;
            var password = $scope.model.password;
            var repeatEmail = $scope.model.repeatEmail;
            var repeatPassword = $scope.model.repeatPassword;

            if (!code) return error('errorCode', 'Debes introducir o código que aparece na túa pantalla');
            if (!email) return error('errorEmail', 'Debes introducir unha dirección de email');
            if (!password) return error('errorPassword', 'Debes introducir unha contraseña de 6 caracteres ou mais');
            if (!repeatEmail) return error('errorRepeatEmail', 'Debes confirmar o email');
            if (!repeatPassword) return error('errorRepeatPassword', 'Debes confirmar o teu contrasinal');
            if (email != repeatEmail) return error('errorRepeatEmail', 'Los emails no coinciden');
            if (password != repeatPassword) return error('errorRepeatPassword', 'os contrassinais non coinciden');
            if (password.length < 6) return error('errorPassword', 'o contrasinal debe ter 6 caracteres ou máis');

            UserService.save({
                user: $scope.model.email,
                password: $scope.model.password,
                screen: $scope.model.code
            }, function(data) {
                store.set(USER, data.user);
                store.set(PASSWORD, data.password);
                store.set(REMEMBER, true);
                $location.path('album');
            }, function(err) {
                handleError(err.status);
            });
        };

        function error(field, msg) {
            $scope.model[field] = msg;
        }

        function handleError(code) {
            switch (code) {
                case 400: return alert('erro: faltan parámetros');
                case 404: return alert('código de pantalla especificado incorrecto.');
                case 405: return alert('método HTTP non soportado.');
                case 409: return alert('código de pantalla asociado a outro usuario ou o usuario xa existe. ¿Xa tES cOnta en promo tv? Accede cás túas credenciaies.');
                case 500: return alert('Error interno. Estamos trabajando para solucionarlo. Inténtelo de nuevo pasados unos minutos.');
                default: return alert('Erro descoñecido. Inténteo de novo pasados uns minutos.');
            }
        }

        function cleanErrors() {
            $scope.model.errorCode = $scope.model.errorEmail = $scope.model.errorPassword =
            $scope.model.errorRepeatEmail = $scope.model.errorRepeatPassword = false;
        }
    }
]);