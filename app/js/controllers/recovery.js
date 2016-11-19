angular.module('angularApp').controller('RecoveryController', [
    '$scope',
    'RecoveryService',
    function($scope, RecoveryService) {

        $scope.model = {
            email: null,
            errorEmail: null,
            success: false
        };

        $scope.submit = function() {
            $scope.model.errorEmail = false;
            $scope.model.success = false;
            var email = $scope.model.email;
            if (!email) {
                $scope.model.errorEmail = "Debe especificar un email";
                return;
            }
            RecoveryService.save({
                email: email
            }, function() {
                $scope.model.success = true;
            }, function(err) {
                handleError(err.status);
            });
        };

        function handleError(status) {
            switch (status) {
                case 400:
                    $scope.model.errorEmail = 'petición incorrecta. Falta algún parámetro obrigatorio.';
                    break;
                case 404:
                    $scope.model.errorEmail = 'o email especificado non pertence a ningún usuario.';
                    break;
                case 500:
                    $scope.model.errorEmail = 'Erro interno. Inténteo de novo pasados uns minutos.';
                    break;
                default:
                    $scope.model.errorEmail = 'Erro descoñecido. Inténteo de novo pasados unos minutos.';
                    break;
            }
        }
    }
]);