//Script for making menu on scroll as it is on fossasia.org
$(window).scroll(function() {
		let languages = ["bg","de","eo","es","fr","gj","hi","id","xy","tl","tm","tr","vi","zh_tw"];
		let path = $(window)[0].location.pathname.slice(1,3);
		let translated = languages.includes(path);
        if ($(window).scrollTop() > 1) {
            $('.navbar-default').addClass('changed-nav');
            if(translated)
            	$('#logo').replaceWith('<img id="logo" src="../img/logo_dark.svg" alt="Logo of the campaign" />');
            else
            	$('#logo').replaceWith('<img id="logo" src="img/logo_dark.svg" alt="Logo of the campaign" />');
        } else {
            $('.navbar-default').removeClass('changed-nav');
            if(translated)
            	$('#logo').replaceWith('<img id="logo" src="../img/logo.svg" alt="Logo of the campaign" />');
            else
            	$('#logo').replaceWith('<img id="logo" src="img/logo.svg" alt="Logo of the campaign" />');
        }
    });

