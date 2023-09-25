jQuery(document).ready(function($) {

    $( ".ctc-bg-share" ).click(function() {
        $( ".dials" ).removeClass("clozed");
        $( ".dials" ).toggleClass( "active");
        $( ".ctc-zad" ).toggleClass( "show");

    });

    $('body').on('click', '.ctc-zad', function () {
        $( ".dials" ).removeClass("clozed");
        $('.dials').removeClass('active');
        $('.ctc-zad').removeClass('show');

    });

});