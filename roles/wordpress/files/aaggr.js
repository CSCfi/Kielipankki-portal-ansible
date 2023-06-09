//FILL THESE
var shibbolethSessionUrl = "/Shibboleth.sso/Session",
    spEntityID = "https://sp.www.kielipankki.fi",
    aggregator_url='https://clarin-aa.ms.mff.cuni.cz/aaggreg/v1/got';

jQuery.get(shibbolethSessionUrl).
    done(function(data){
       var session = jQuery(data);
       var idp = session.find("strong:contains('Identity Provider:')")[0].nextSibling.nodeValue.trim();
       var attrs = session.find("u:contains('Attributes')").nextAll().map(function(index, el){ return encodeURIComponent(el.innerHTML)}).get();
       if(idp){
            var ts = new Date().toISOString();
            attributes_encoded = "attributes[]=" + attrs.join("&attributes[]=");
            var logUrl = aggregator_url + '?idp=' + idp + '&sp=' + spEntityID + '&timestamp=' + ts + '&' + attributes_encoded + '&source=js_aaggr'; 
            jQuery.get(logUrl).done(function(){
                console.log("Succcessfully sent " + logUrl);
		// Handover to AttrChecker
		document.location.href = '/Shibboleth.sso/AttrChecker' + document.location.search;
            });
       }

});
