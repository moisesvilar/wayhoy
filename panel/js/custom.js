$(document).ready(function() {

    $('.confirm').click(function(e) {
        e.preventDefault();
        if(confirm('¿Estás seguro?')) {
            window.location = $(this).attr('data-url');
        }
    });

    $('.select2').select2();

    $('input[type=checkbox]').click(function() {
        var $this = $(this);
        var selector = 'input[name=' + $this.attr('id') + ']';
        $(selector).val($this.is(':checked') ? '1' : '0');
    });

});
