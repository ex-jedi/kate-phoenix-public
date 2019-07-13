//Responsive menu
$(function() {
    var pull        = $('#pull');
        menu        = $('.responsive-main-nav');

    $(pull).on('click', function(e) {
        e.preventDefault();
        menu.slideToggle();
        ($("#pull-span").text() === "HIDE\xa0") ? $("#pull-span").text("SHOW\xa0") : $("#pull-span").text("HIDE\xa0");
    });
});

//Restores menu when screen is resized to larger than menu breakpoint, and preserves responsive menu state.
$(window).resize(function(){
    if(($("#pull").css("display") == "none" ) && menu.is(':hidden')) {
        menu.removeAttr('style');
    }
});

//Sticky Navigation



$(window).scroll(function() {

  var mainNav = $(".main-nav");
  var legalsNav = $(".legals-nav");
  var stickyNav = "sticky-nav";
  var legalsStickyNav = "legals-sticky-nav";
  var headerHeight = $('.main-header').height();

  if( ($(this).scrollTop() > headerHeight) && ($("#pull").css("display") == "block" ) ) {
    mainNav.addClass(stickyNav);
    legalsNav.addClass(legalsStickyNav);

  } else {
    mainNav.removeClass(stickyNav);
    legalsNav.removeClass(legalsStickyNav);
  }
});
