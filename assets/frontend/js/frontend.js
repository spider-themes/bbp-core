(function($){
    $(document).ready(function(){        
        $(document).on('click', '.is-subscribed a', function(e){   
            e.preventDefault();         
            $(this).text( "Unsubscribing..." );    
            $('.bbpc-unsubscribe-link').text( "Unsubscribing..." );
            setTimeout(function(){
                $( ".bbp__success-subscribe" ).hide();
            }
            , 1100);
        });
        
        $(document).on('click', '.show_subscribe span:not(.is-subscribed) a', function(){            
            $( ".show_subscribe span:not(.is-subscribed) a" ).text( "Subscribing..." );
        });

        // if has class
        if ( $( ".show_subscribe .is-subscribed" ).length ) {
            $( ".post-header" ).before( '<div class="alert alert-success bbp__success-subscribe"><div class="mailIcon"><svg width="40" height="40" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path data-name="envelope-Filled" d="M10.36 11.71 3.19 6.62A4.5 4.5 0 0 1 7 4.5h10a4.5 4.5 0 0 1 3.81 2.11l-7.15 5.08a3 3 0 0 1-3.3.02ZM21.48 8.6l-6.68 4.74a5.082 5.082 0 0 1-2.81.85 4.968 4.968 0 0 1-2.76-.84L2.52 8.6c-.01.13-.02.27-.02.4v6A4.507 4.507 0 0 0 7 19.5h10a4.507 4.507 0 0 0 4.5-4.5V9c0-.13-.01-.27-.02-.4Z" style="fill:#fff"/></svg></div><span>You are subscribed to this forum, and will receive emails for future topic activity.</span> <a class="bbpc-unsubscribe-link" data-forum-id='+bbpc_localize_script.bbpc_subscribed_forum_id+' href="javascript:void()">Unsubscribe</a> from '+bbpc_localize_script.bbpc_subscribed_forum_title+'</div>' );
        }
        
        $('.bbpc-unsubscribe-link').click(function(){
            let data_id = $(this).attr('data-forum-id');
            $('#subscribe-'+data_id+' a').click().text( "Unsubscribing..." );
            $(this).text( "Unsubscribing..." );
        });

    });
})(jQuery);

(function($){ 
    $(document).ready(function(){

        // Focus in search input
        $('.bbpc_search_form_wrapper').focusin(function () {
            $('body').addClass('bbpc-search-active');
            $('body.bbpc-search-active').append('<div class="bbpc-search-overlay"></div>');
        });
        
        // Focus out search input
        $('.bbpc_search_form_wrapper').focusout(function () {
            $('body').removeClass('bbpc-search-active');
            $('.bbpc-search-overlay').remove();
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
})(jQuery)

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