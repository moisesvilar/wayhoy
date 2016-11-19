angular.module('angularApp').directive('ngConfirm', [
    function(){
        return {
            link: function (scope, element, attr) {
                var msg = attr.ngConfirmMessage || "¿Estás seguro?";
                var clickAction = attr.ngConfirmAction;
                element.bind('click',function (event) {
                    if (window.confirm(msg) ) {
                        scope.$eval(clickAction)
                    }
                });
            }
        };
    }
]);