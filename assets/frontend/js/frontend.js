(function ($) {
    $(document).ready(function () {
        const overlay = $('.bbpc-search-overlay');

        $('#searchInput, #bbpc-search-result, .bbpc-search-keyword ul li a').on('click', function () {
            overlay.css('display', 'block');
            overlay.addClass('active');

            overlay.on('click', function () {
                overlay.css('display', 'none');
                overlay.removeClass('active');
            });

            // Focus in search input
            $('.bbpc_search_form_wrapper').focusin(function () {
                $('.body_dark #searchInput').addClass('input_focused');
                if ($('#bbpc-search-result.ajax-search').length > 0) {
                    $('.body_dark #searchInput').addClass('input_focused');
                }
            });

            // Focus out search input
            $('.bbpc_search_form_wrapper').focusout(function () {
                if ($('#bbpc-search-result.ajax-search').length > 0) {
                    $('.body_dark #searchInput').addClass('input_focused');
                } else {
                    $('.body_dark #searchInput').removeClass('input_focused');
                }
            });

            $('#searchInput').keyup(function () {
                $('.click_capture').css({ 'opacity': '0', 'visibility': 'hidden' });
            });

            // Keyup in search input
            $('#searchInput').keyup(function () {
                $('.not-found-text').css('display', 'none');
                var searchInput = $(this).val();
                var ajax_url = bbpc_localize_script.ajaxurl;

                if (searchInput != '') {
                    $.ajax({
                        url: ajax_url,
                        method: 'POST',
                        data: {
                            action: 'bbpc_search_data_fetch',
                            keyword: searchInput
                        },
                        beforeSend: function () {
                            $('.spinner').css('display', 'block');
                        },
                        success: function (data) {
                            $('#bbpc-search-result').html(data).addClass('ajax-search');
                            $('.spinner').css('display', 'none');

                            var no_result = $('.tab-item.active.all-active').attr('data-noresult');
                            if (no_result) {
                                no_result = no_result.replace("-", " ");
                                no_result = no_result.replace("-", " ");
                                $('#bbpc-search-result').html('<h5 class="bbpc-not-found-text">' + no_result + '</h5>');
                                $('.bbpc-not-found-text').css('display', 'block');
                            }
                        },
                        error: function (xhr, status, error) {
                            // Handle the error
                        }
                    });
                }
            });

            // Keywords
            $('.bbpc-search-keyword ul li a').on('click', function (e) {
                e.preventDefault();
                var content = $(this).text();
                $('#searchInput').val(content).focus();
                var ajax_url = bbpc_localize_script.ajaxurl;

                if (content != '') {
                    $.ajax({
                        url: ajax_url,
                        method: 'POST',
                        data: {
                            action: 'bbpc_search_data_fetch',
                            keyword: content
                        },
                        beforeSend: function () {
                            $('.spinner').css('display', 'block');
                        },
                        success: function (data) {
                            $('#bbpc-search-result').html(data).addClass('ajax-search');
                            $('.spinner').css('display', 'none');

                            var no_result = $('.tab-item.active.all-active').attr('data-noresult');
                            if (no_result) {
                                no_result = no_result.replace("-", " ");
                                no_result = no_result.replace("-", " ");
                                $('#bbpc-search-result').html('<h5 class="bbpc-not-found-text">' + no_result + '</h5>');
                                $('.bbpc-not-found-text').css('display', 'block');
                            }
                        },
                        error: function (xhr, status, error) {
                            // Handle the error
                        }
                    });
                }
            });

            // Clear search input
            $('#searchInput').on('input', function (e) {
                if ('' == this.value) {
                    $('#bbpc-search-result').removeClass('ajax-search');
                }
            });
        });

        $("#searchInput").focus(function () {
            $('body').addClass('bbpc-search-overlay');
            $('form.bbpc_search_form_wrapper').css('z-index', '999');
        })

        $(".bbpc-search-overlay").click(function () {
            $('body').removeClass('bbpc-search-overlay');
            $('form.bbpc_search_form_wrapper').css('z-index', 'unset');
        })
    });
})(jQuery);
