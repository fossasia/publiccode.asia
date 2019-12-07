// hiding scroll-to-top on top and showing on scroll
// using jQuery
$(document).ready(function(){
           
    $(window).scroll(function(){
        if($(window).scrollTop()>$(".arrow-bounce").height()/2)
        {
          $("#scroll-to-top").fadeIn()
        }

        else 
        {
          $("#scroll-to-top").fadeOut()
        }
    }
    )
})