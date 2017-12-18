//Script for making menu on scroll as it is on fossasia.org
$(window).scroll(function() {
        let count = $(window)[0].location.pathname.split("/").length-2;
        let lightLogoPath = "img/logo.svg";
        let darkLogoPath = "img/logo_dark.svg";
        for (var i=0; i<count; i++){
            lightLogoPath = "../" + lightLogoPath;
            darkLogoPath = "../" + darkLogoPath;
        }
        if ($(window).scrollTop() > 1) {
            $('.navbar-default').addClass('changed-nav');
            $('#logo').replaceWith('<img id="logo" src=\"' + darkLogoPath + '\" alt="Logo of the campaign" />');
        } else {
            $('.navbar-default').removeClass('changed-nav');
            $('#logo').replaceWith('<img id="logo" src=\"' + lightLogoPath + '\" alt="Logo of the campaign" />');
        }
    });
