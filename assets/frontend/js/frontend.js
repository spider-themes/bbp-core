(function($){ 
    $(document).ready(function(){
        
        const overlay = $('.bbpc-search-overlay');

        $('#searchInput, #bbpc-search-result, .bbpc-search-keyword ul li a').on('click', function() {
            overlay.css('display', 'block');
            // addClass
            overlay.addClass('active');
            
            // Focus in search input
            $('.bbpc_search_form_wrapper').focusin(function () {

                $('.body_dark #searchInput').addClass('input_focused');

                if ( $('#bbpc-search-result.ajax-search').length > 0 ) {
                    $('.body_dark #searchInput').addClass('input_focused');
                }
            });
            
            // Focus out search input
            $('.bbpc_search_form_wrapper').focusout(function () {
                
                if ( $('#bbpc-search-result.ajax-search').length > 0 ) {
                    $('.body_dark #searchInput').addClass('input_focused');
                } else {
                    $('.body_dark #searchInput').removeClass('input_focused');
                }
            });
            
            $('#searchInput').keyup(function(){
                $('.click_capture').css({'opacity':'0', 'visibility':'hidden'});
            });
        });

        overlay.on('click', function() {
            overlay.css('display', 'none');
            overlay.removeClass('active');
        });
        
        // Keyup in search input
        $('#searchInput').keyup(function(){
            
            $('.not-found-text').css('display', 'none');
            var searchInput = $(this).val();
            var ajax_url    = bbpc_localize_script.ajaxurl;

            if ( searchInput != '' ) {
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
                    success: function(data) {
                        // Handle the response
                        $('#bbpc-search-result').html(data).addClass('ajax-search');
                        $('.spinner').css('display', 'none');
                        
                        var no_result = $('.tab-item.active.all-active').attr('data-noresult');
                        if ( no_result ) {
                            no_result = no_result.replace("-"," ");
                            no_result = no_result.replace("-"," ");
                            $('#bbpc-search-result').html('<h5 class="bbpc-not-found-text">'+no_result+'</h5>');
                            $('.bbpc-not-found-text').css('display', 'block');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                    }
                });
            }
        });

        // Tabs 
        $(document).on( 'click', 'body:not(.search) .searchbar-tabs .tab-item', function (e) {
            $('.searchbar-tabs .tab-item').removeClass('active');
            $(this).addClass('active');
        } );

        // Keywords
        $('.bbpc-search-keyword ul li a').on('click', function (e) {
            e.preventDefault();
            var content     = $(this).text();
            $('#searchInput').val(content).focus();            
            var ajax_url    = bbpc_localize_script.ajaxurl;

            if ( content != '' ) {
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
                    success: function(data) {
                        // Handle the response
                        $('#bbpc-search-result').html(data).addClass('ajax-search');
                        $('.spinner').css('display', 'none');
                        
                        var no_result = $('.tab-item.active.all-active').attr('data-noresult');
                        if ( no_result ) {
                            no_result = no_result.replace("-"," ");
                            no_result = no_result.replace("-"," ");
                            $('#bbpc-search-result').html('<h5 class="bbpc-not-found-text">'+no_result+'</h5>');
                            $('.bbpc-not-found-text').css('display', 'block');
                        }
                    },
                    error: function(xhr, status, error) {
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
})(jQuery);

// All Tabs
function searchAllTab() {

    let keyword = jQuery('#searchInput').val();
    var ajax_url    = bbpc_localize_script.ajaxurl;
    
    jQuery('#search-docs-results').hide();
    jQuery('#search-forum-results').hide();
    jQuery('#search-blog-results').hide(); 
    
    jQuery.ajax({
        url: ajax_url,
        method: 'POST',
        data: {
            action: 'bbpc_search_data_fetch',
            keyword: keyword
        },
        beforeSend: function () {
            jQuery('.spinner').css('display', 'block');
        },
        success: function(data) {
            // Handle the response
            jQuery('#bbpc-search-result').html(data).addClass('ajax-search');
            jQuery('.spinner').css('display', 'none');
            jQuery('#search-docs-results').show();
            jQuery('#search-forum-results').show();
            jQuery('#search-blog-results').show();
        },
        error: function(xhr, status, error) {
            // Handle the error
        }
    });
}

// Forum Tab
function searchForumTab() {

    let keyword     = jQuery('#searchInput').val();
    var ajax_url    = bbpc_localize_script.ajaxurl;
    
    jQuery('#search-docs-results').hide();
    jQuery('#search-forum-results').hide();
    jQuery('#search-blog-results').hide();
    jQuery('.not-found-text').css('display', 'none');

    jQuery.ajax({
        url: ajax_url,
        method: 'POST',
        data: {
            action: 'bbpc_search_data_forum',
            keyword: keyword
        },
        beforeSend: function () {
            jQuery('.spinner').css('display', 'block');
          },
        success: function(data) {
            // Handle the response
            jQuery('#search-forum-results').html(data).addClass('ajax-search').show();
            jQuery('.spinner').css('display', 'none');
            jQuery('#search-forum-results').show();

            var no_result = jQuery('.tab-item.active').attr('data-noresult');           
            if ( no_result ) {
                jQuery('.not-found-text').css('display', 'block');
            }
        },
        error: function(xhr, status, error) {
            // Handle the error
        }
    });
}

// Docs Tab
function searchDocTab() {
    let keyword     = jQuery('#searchInput').val();
    var ajax_url    = bbpc_localize_script.ajaxurl;
    var post_type   = 'docs';

    jQuery('#search-docs-results').hide();
    jQuery('#search-forum-results').hide();
    jQuery('#search-blog-results').hide();
    jQuery('.not-found-text').css('display', 'none');
    
    jQuery.ajax({
        url: ajax_url,
        method: 'POST',
        data: {
            action: 'bbpc_search_data_blog',
            keyword: keyword,
            post_type: post_type
        },
        beforeSend: function () {
            jQuery('.spinner').css('display', 'block');
        },
        success: function(data) {
            // Handle the response
            jQuery('#search-docs-results').html(data).addClass('ajax-search').show();
            jQuery('.spinner').css('display', 'none');

            var no_result = jQuery('.tab-item.active').attr('data-noresult');           
            if ( no_result ) {
                jQuery('.not-found-text').css('display', 'block');
            }
        },
        error: function(xhr, status, error) {
            // Handle the error
        }
    });
}
  
function searchBlogTab() {
    let keyword     = jQuery('#searchInput').val();
    var ajax_url    = bbpc_localize_script.ajaxurl;
    var post_type   = 'post';
  
    jQuery('#search-docs-results').hide();
    jQuery('#search-forum-results').hide();
    jQuery('#search-blog-results').hide();
    jQuery('.not-found-text').css('display', 'none');

    jQuery.ajax({
        url: ajax_url,
        method: 'POST',
        data: {
            action: 'bbpc_search_data_blog',
            keyword: keyword,
            post_type: post_type
        },
        beforeSend: function () {
            jQuery('.spinner').css('display', 'block');
          },
        success: function(data) {
            // Handle the response
            jQuery('#search-blog-results').html(data).addClass('ajax-search').show();
            jQuery('.spinner').css('display', 'none');

            var no_result = jQuery('.tab-item.active').attr('data-noresult');   
            if ( no_result ) {
                jQuery('.not-found-text').css('display', 'block');
            }
        },
        error: function(xhr, status, error) {
            // Handle the error
        }
    });
  }