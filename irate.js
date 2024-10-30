function irate_feed( )
	{
		var irate_sack = new sack(irate_url+'/wp-admin/admin-ajax.php' );
		
		//Our plugin sack configuration
		irate_sack.execute = 0;
		irate_sack.method = 'POST';
		irate_sack.setVar( 'action', 'irate_ajax' );
		irate_sack.element = 'irate_content';
		
		//What to do on error?
		irate_sack.onError = function() {
			var aux = document.getElementById(irate_sack.element);
			aux.innerHTMLsetAttribute=irate_i18n_error;
		};
		
		irate_sack.runAJAX();
		
		return true;
	} // end of JavaScript function irate_feed
