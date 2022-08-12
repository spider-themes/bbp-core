(function ($) {
  "use strict";

  /*------------ Cookie functions and color js ------------*/
  function createCookie(name, value, days) {
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
  }

  function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  function eraseCookie(name) {
    createCookie(name, "", -1);
  }

  var prefersDark =
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches;
  var selectedNightTheme = readCookie("body_dark");

  if (
    selectedNightTheme == "true" ||
    (selectedNightTheme === null && prefersDark)
  ) {
    applyNight();
    $(".dark_mode_switcher").prop("checked", true);
  } else {
    applyDay();
    $(".dark_mode_switcher").prop("checked", false);
  }

  function applyNight() {
    if ($(".js-darkmode-btn .ball").length) {
      $(".js-darkmode-btn .ball").css("left", "45px");
    }
    $("body").addClass("body_dark");
  }

  function applyDay() {
    if ($(".js-darkmode-btn .ball").length) {
      $(".js-darkmode-btn .ball").css("left", "4px");
    }
    $("body").removeClass("body_dark");
  }

  $(".dark_mode_switcher").change(function () {
    if ($(this).is(":checked")) {
      applyNight();

      createCookie("body_dark", true, 999);
    } else {
      applyDay();
      createCookie("body_dark", false, 999);
    }
  });

  // Filter Select
  $('select').niceSelect();

  // Sidebar Tabs
  $('.tab-menu .easydocs-navitem').on('click', function () {
    var target = $(this).attr('data-rel');
    $('.tab-menu .easydocs-navitem').removeClass('is-active');
    $(this).addClass('is-active');
    $("#" + target).fadeIn('slow').siblings(".easydocs-tab").hide();
    return false;
  });

  $(".accordionjs").accordionjs({
    activeIndex: false,
    closeAble: false,
  });

  $(".sortable").sortable({
    placeholder: "ui-state-highlight",
    classes: {
      "ui-draggable": "highlight"
    }
  });
  $(".sortable").disableSelection();

  jQuery(document).ready(function (e) {
    function t(t) {
      e(t).bind("click", function (t) {
        t.preventDefault();
        e(this).parent().fadeOut()
      })
    }
    e(".header-notify-icon").click(function () {
      var t = e(this).parents(".easydocs-notification").children(".easydocs-dropdown").is(":hidden");
      e(".easydocs-notification .easydocs-dropdown").hide();
      e(".easydocs-notification .header-notify-icon").removeClass("active");
      if (t) {
        e(this).parents(".easydocs-notification").children(".easydocs-dropdown").toggle().parents(".easydocs-notification").children(".header-notify-icon").addClass("active")
      }
    });
    e(document).bind("click", function (t) {
      var n = e(t.target);
      if (!n.parents().hasClass("easydocs-notification")) e(".easydocs-notification .easydocs-dropdown").hide();
    });
    e(document).bind("click", function (t) {
      var n = e(t.target);
      if (!n.parents().hasClass("easydocs-notification")) e(".easydocs-notification .header-notify-icon").removeClass("active");
    })
  });

})(jQuery);

function menuToggle() {
  const toggleMenu = document.querySelector(".easydocs-dropdown");
  toggleMenu.classList.toggle('is-active')
}

// var el = document.getElementById('sortable');
// var sortable = Sortable.create(el);

var containerEl1 = document.querySelector('[data-ref="container-1"]');
var containerEl2 = document.querySelector('[data-ref="container-2"]');
var containerEl3 = document.querySelector('[data-ref="container-3"]');

var config = {
  controls: {
    scope: 'local'
  }
};

var mixer1 = mixitup(containerEl1, config);
var mixer1 = mixitup(containerEl2, config);
var mixer1 = mixitup(containerEl3, config);

