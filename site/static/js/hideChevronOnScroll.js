//script to hide/show the "move to top" chevron on scroll
$(window).scroll(function() {
  if ($(this).scrollTop() > 700) {
    $("#scroll-to-top").fadeIn();
  } else {
    $("#scroll-to-top").fadeOut();
  }
});
