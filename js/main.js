// Gumby is ready to go
Gumby.ready(function() {
	Gumby.log('Gumby is ready to go...', Gumby.dump());

	// placeholder polyfil
	if(Gumby.isOldie || Gumby.$dom.find('html').hasClass('ie9')) {
		jQuery('input, textarea').placeholder();
	}

	// skip link and toggle on one element
	// when the skip link completes, trigger the switch
	jQuery('#skip-switch').on('gumby.onComplete', function($) {
		$(this).trigger('gumby.trigger');
	});
        
    jQuery('#primary-search-btn').on(Gumby.click, function(e) { 
        e.preventDefault();
         if(jQuery.trim(jQuery('#primary-search-input').val()).length != 0){
            jQuery('#primary-search-form').submit();
        }
    });

// Oldie document loaded
}).oldie(function() {
	Gumby.warn("This is an oldie browser...");

// Touch devices loaded
}).touch(function() {
	Gumby.log("This is a touch enabled device...");
});


jQuery(document).ready(function($){

    //tablesorter.js
    $(".retailer-table").tablesorter( {
        headers: {
            0: {sorter: false},
            1: {sorter: false},
            2: {sorter: false}
        }, //disable sorting on the first 3 columns of a table
        cssHeader: "sort-head"
    }); 
     
    // list and grid view switch
    $('#list-toggle .list-layout-switch, #list-toggle .grid-layout-switch').live('click', function(event){
        event.preventDefault();
        $('#list-toggle li').removeClass('active');
        $('.product-listing-container').removeClass('grid-view');
        $('.product-listing-container').removeClass('list-view');
        if( $(this).hasClass('grid-layout-switch') ) {
             $('.product-listing-container').addClass('grid-view');
             $(this).parent('li').addClass('active')
        }
        if( $(this).hasClass('list-layout-switch') ) {
             $('.product-listing-container').addClass('list-view');
             $(this).parent('li').addClass('active')
        }
    })
    
    $('#primary-search-input').keydown(function(e) {            
            if(e.keyCode == 13 || e.which == 13){
                e.preventDefault();              
                if($.trim($(this).val()).length != 0){                    
                    $('#primary-search-form').submit();
                }
            }
    });

    //Toggle shortcode
    jQuery('.toggle-content').hide();
    jQuery('.toggle-button').click(function(e) {
        e.preventDefault();
        if(jQuery(this).hasClass('active') == true) {
            jQuery(this).removeClass('active');
            jQuery(this).next('.toggle-content').slideUp('fast');
        } else { 
            jQuery(this).addClass('active');
            jQuery(this).next('.toggle-content').slideDown('fast');
        }
    });
});