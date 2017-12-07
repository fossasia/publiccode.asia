//Script for making menu on scroll as it is on fossasia.org
$(window).scroll(function() {
        if ($(window).scrollTop() > 1) {
            $('.navbar-default').addClass('changed-nav');
            $('#logo').replaceWith('<img id="logo" src="img/logo_dark.svg" alt="Logo of the campaign" />');
        } else {
            $('.navbar-default').removeClass('changed-nav');
            $('#logo').replaceWith('<img id="logo" src="img/logo.svg" alt="Logo of the campaign" />');
        }
    });

