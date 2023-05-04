// JavaScript Document
jQuery(document).ready(function(e) {
       polyCalc.run();
	   jQuery('.openerbox').find('.openercontent').hide('fast');
	jQuery('.openerbox').find('.infoicon>a').click(function(e){
		e.preventDefault();
		jQuery(this).parent().parent('.openerbox').find('.openercontent').show('fast');
	});
	jQuery(window).resize(function() {
    	polyCalc.run();
		 jQuery('.page-nav>ul').removeClass('opened');
	});
	jQuery('a.mobile-nav-trigger').click(function(e) {
		e.preventDefault();
        jQuery('.page-nav>ul').toggleClass('opened');
    });
	
});