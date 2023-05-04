// JavaScript Document jarno marttila
jQuery(document).ready(function(e) {
       polyCalc.run();
	   jQuery('.openerbox').find('.openercontent').hide('fast');
	jQuery('.openerbox').find('.infoicon>a').click(function(e){
		e.preventDefault();
		jQuery(this).parent().parent('.openerbox').find('.openercontent').show('fast');
	});
	jQuery(window).resize(function() {
    	polyCalc.run();
		 jQuery('.page-nav ul.nav').removeClass('opened');
	});
	jQuery('a.mobile-nav-trigger').click(function(e) {
		e.preventDefault();
        jQuery('.page-nav ul.nav').toggleClass('opened');
    });
	
});

function Popup(url, width, height) {
    wLeft = window.screenLeft ? window.screenLeft : window.screenX;
    wTop = window.screenTop ? window.screenTop : window.screenY;

    var left = wLeft + (window.innerWidth / 2) - (width / 2);
    var top = wTop + (window.innerHeight / 2) - (height / 2);
    var style = "width="+width+", height="+height+", top="+top+", left="+left+", status=no, menubar=no, toolbar=no scrollbar=no";

    window.open(url, "", style);
}

function RefPopup(corpus_id) {
    var lang =  $('body').attr('lang');
    Popup("/viittaus?key="+encodeURIComponent(corpus_id)+"&lang="+lang, 800, 600);
}
