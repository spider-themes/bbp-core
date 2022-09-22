(function ($) {
  'use strict';

  /*------------ Cookie functions and color js ------------*/
  function createCookie(name, value, days) {
    var expires = '';
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + value + expires + '; path=/';
  }

  function readCookie(name) {
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  function eraseCookie(name) {
    createCookie(name, '', -1);
  }

  var prefersDark =
    window.matchMedia &&
    window.matchMedia('(prefers-color-scheme: dark)').matches;
  var selectedNightTheme = readCookie('body_dark');

  if (
    selectedNightTheme == 'true' ||
    (selectedNightTheme === null && prefersDark)
  ) {
    applyNight();
    $('.dark_mode_switcher').prop('checked', true);
  } else {
    applyDay();
    $('.dark_mode_switcher').prop('checked', false);
  }

  function applyNight() {
    if ($('.js-darkmode-btn .ball').length) {
      $('.js-darkmode-btn .ball').css('left', '45px');
    }
    $('body').addClass('body_dark');
  }

  function applyDay() {
    if ($('.js-darkmode-btn .ball').length) {
      $('.js-darkmode-btn .ball').css('left', '4px');
    }
    $('body').removeClass('body_dark');
  }

  $('.dark_mode_switcher').change(function () {
    if ($(this).is(':checked')) {
      applyNight();
      createCookie('body_dark', true, 999);
    } else {
      applyDay();
      createCookie('body_dark', false, 999);
    }
  });

  // Filter Select
  $('select').niceSelect();

  // Sidebar Tabs [COOKIE]
  $(document).on('click', '.tab-menu .easydocs-navitem', function () {
    let target = $(this).attr('data-rel');
    $('.tab-menu .easydocs-navitem').removeClass('is-active');
    $(this).addClass('is-active');
    $('#' + target)
      .fadeIn('slow')
      .siblings('.easydocs-tab')
      .hide();

    let is_active_tab = $('.tab-menu .easydocs-navitem').hasClass('is-active');
    if (is_active_tab === true) {
      let active_tab_id = $('.easydocs-navitem.is-active').attr('data-rel');
      createCookie('eazydocs_doc_current_tab', active_tab_id, 999);
    }

    return true;
  });

  // Remain the last active doc tab
  function keep_last_active_doc_tab() {
    let doc_last_current_tab = readCookie('eazydocs_doc_current_tab');
    if (doc_last_current_tab) {
      // Tab item
      $('.tab-menu .easydocs-navitem').removeClass('is-active');
      $(
        '.tab-menu .easydocs-navitem[data-rel=' + doc_last_current_tab + ']'
      ).addClass('is-active');
      // Tab content
      $('.easydocs-tab-content .easydocs-tab').removeClass('tab-active');
      $('#' + doc_last_current_tab).addClass('tab-active');
    }
  }
  keep_last_active_doc_tab();

  $('.tab-menu .easydocs-navitem .parent-delete').on('click', function () {
    return false;
  });

  $(document).ready(function () {
    $("#bbpc-search").on("keyup", function() {
     var value = $(this).val().toLowerCase();
     $(".easydocs-accordion-item").filter(function() {
         $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
     })
   });

   // Dropdown Classic UI Filter
   let bbpcClassicUi = document.getElementById('bbpcClassicUi');

   function swithToLink(){
     window.location.href=this.value;
   }

   bbpcClassicUi.onchange = swithToLink;
});

  $(document).ready(function (e) {
    function t(t) {
      e(t).bind('click', function (t) {
        t.preventDefault();
        e(this).parent().fadeOut();
      });
    }

    e('.header-notify-icon').click(function () {
      var t = e(this)
        .parents('.easydocs-notification')
        .children('.easydocs-dropdown')
        .is(':hidden');
      e('.easydocs-notification .easydocs-dropdown').hide();
      e('.easydocs-notification .header-notify-icon').removeClass('active');
      if (t) {
        e(this)
          .parents('.easydocs-notification')
          .children('.easydocs-dropdown')
          .toggle()
          .parents('.easydocs-notification')
          .children('.header-notify-icon')
          .addClass('active');
      }
    });
    e(document).bind('click', function (t) {
      var n = e(t.target);
      if (!n.parents().hasClass('easydocs-notification'))
        e('.easydocs-notification .easydocs-dropdown').hide();
    });
    e(document).bind('click', function (t) {
      var n = e(t.target);
      if (!n.parents().hasClass('easydocs-notification'))
        e('.easydocs-notification .header-notify-icon').removeClass('active');
    });
    
    // ADD PARENT FORUM
    function create_forum() {
      $(document).on('click', '#bbpc-forum', function (e) {
          e.preventDefault();
          let href = $(this).attr('href')
          Swal.fire({
              title: bbp_core_local_object.create_forum_title,
              input: 'text',
              showCancelButton: true,
              inputAttributes: {
                  name: 'parent_title'
              },
          }).then((result) => {
              if (result.value) {
                  document.location.href = href + result.value;
              }
          })
      })
  }
  create_forum();

  // SECTION DOC
  function create_topic() {
    $(document).on('click', '#bbpc-topic', function (e) {
        e.preventDefault();
        let href = $(this).attr('href')
        Swal.fire({
            title: bbp_core_local_object.create_topic_title,
            input: 'text',
            showCancelButton: true,
            inputAttributes: {
                name: 'section'
            },
        }).then((result) => {
            if (result.value) {
                document.location.href = href + result.value;
            }
        })
    })
  } 
  create_topic();

    // DELETE FORUM
    function delete_forum() {
      $('.forum-delete').on('click', function (e) {
          e.preventDefault();
          let href = $(this).attr('href')
          Swal.fire({
              title: bbp_core_local_object.forum_delete_title,
              text: bbp_core_local_object.forum_delete_desc,
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes'
          }).then((result) => {
              if (result.value) {
                  document.location.href = href;
              }
          })
      })
  }
  delete_forum()

  // DELETE TOPIC
  function delete_topic() {
    $('.section-delete').on('click', function (e) {
        e.preventDefault();
        let href = $(this).attr('href')
        Swal.fire({
            title: bbp_core_local_object.forum_delete_title,
            text: bbp_core_local_object.topic_delete_desc,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        })
    })
  }
  delete_topic();
  });

  // Click pending replies count to show pending replies.
  $( '[click-target]').click(function(){
    let id = $(this).attr('click-target');
    $(`[click-target=${id}]`).toggleClass('active');
    $(`[reply-target=${id}]`).toggle();
  });

   // Sidebar Tabs [COOKIE]
   $(document).on('click', '[data-filter]', function () {
    let target = $(this).attr('data-filter');
    $('[data-filter]').removeClass('is-active');
    $(this).addClass('is-active');
    $(target)
      .fadeIn('slow')
      .siblings('.easydocs-tab')
      .hide();
      
    let isActiveTab = $(this).hasClass('is-active');
    if (isActiveTab === true) {
      createCookie('bbpc_current_filter', target, 999);
    }

    return true;
  });

   // Keep Last filter item active
   function keep_last_filter_active() {
    let bbpcLastActiveFilter = readCookie('bbpc_current_filter');
    console.log('ID is: ' + bbpcLastActiveFilter);
    // console.log('cookie: ' + bbpcLastActiveFilter);
    console.log(`[data-filter="${bbpcLastActiveFilter}"]`);
    // if (bbpcLastActiveFilter) {
    //   // Tab item
    //   $('[data-filter]').removeClass('is-active');
    //   $(`[data-filter="${bbpcLastActiveFilter}"]`).addClass('is-active');

    //   // Tab content
    //   $('.easydocs-tab-content .easydocs-tab').removeClass('tab-active');
    //   $('#tab' + bbpcLastActiveFilter).addClass('tab-active');
    // }
  }

  keep_last_filter_active();

})(jQuery);

function menuToggle() {
  const toggleMenu = document.querySelector('.easydocs-dropdown');
  toggleMenu.classList.toggle('is-active');
}


// var containerEl1 = document.querySelector('[data-ref="container-1"]');
// var config = {
//     controls: {
//         scope: 'local'
//     }
// };
// var mixer1 = mixitup(containerEl1, config);

