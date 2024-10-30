<?php
/*
Plugin Name: iRate
Version: 0.9.4
Plugin URI: http://www.sebaxtian.com/acerca-de/irate
Description: your iRateMyDay's last rate.
Author: Juan Sebastián Echeverry
Author URI: http://www.sebaxtian.com/
*/

/* Copyright 2007-2010 Juan Sebastián Echeverry (email : sebaxtian@gawab.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

define('IRATE_CACHE_AGE', 600); //Elapsed time to update cache (10 minutes)
define('IRATE_HEADER_V', 1.0);

add_action('wp_head', 'irate_header');
add_action('init', 'irate_text_domain');
add_action('wp_ajax_irate_ajax', 'irate_ajax');
add_action('wp_ajax_nopriv_irate_ajax', 'irate_ajax');

/**
* To declare where are the mo files (i18n).
* This function should be called by an action.
*
* @access public
*/
function irate_text_domain() {
	load_plugin_textdomain('irate', false, 'irate/lang');
}

/**
* Function to add the required data to the header in the site.
* This function should be called by an action.
*
* @access public
*/
function irate_header() {
	
	//Local URL
	$url = get_bloginfo( 'wpurl' );
	$local_url = parse_url( $url );
	$aux_url   = parse_url(wp_guess_url());
	$url = str_replace($local_url['host'], $aux_url['host'], $url);

	//Define custom JavaScript options
	echo "<script type='text/javascript'>
	irate_i18n_error = '".__("Can\'t read iRate Feed", 'irate')."';
	irate_url = '$url';
	</script>
	";

	//Declare javascript
	wp_register_script('irate', $url.'/wp-content/plugins/irate/irate.js', array('sack'), IRATE_HEADER_V);
	wp_enqueue_script('irate');
	
	//Define custom CSS URI
	$css = get_theme_root()."/".get_template()."/irate.css";
	if(file_exists($css)) {
		$css_register = get_bloginfo('template_directory')."/irate.css";
	} else {
		$css_register = irate_plugin_url("/css/irate.css");
	}
	//Declare style
	wp_register_style('irate', $css_register, false, IRATE_HEADER_V);
	wp_enqueue_style('irate');
	
	// Declare we use a script and a style
	wp_print_scripts( array( 'irate' ));
	wp_print_styles( array( 'irate' ));
	
}

/**
* Function to answer the ajax call.
* This function should be called by an action.
*
* @access public
*/
function irate_ajax() {
	//Get the new data.
	$results = irate_content();; 

	// Compose JavaScript for return
	die( $results );
}

/**
* Function to return the url of the plugin concatenated to a string. The idea is to
* use this function to get the entire URL for some file inside the plugin.
*
* @access public
* @param string str The string to concatenate
* @return The URL of the plugin concatenated with the string 
*/
function irate_plugin_url($str = '') {

	$aux = '/wp-content/plugins/irate/'.$str;
	$aux = str_replace('//', '/', $aux);
	$url = get_bloginfo('wpurl');
	return $url.$aux;
	
}

/**
* Function to update cache if the time elapsed is older than the defined one.
*
* @access public
* @param boolean force Defines if we should update the iRate now or if we have
 to wait for the timestamp.
*/
function irate_update( $force = false ) {
	//Suppose we don't have to update
	$update=false;
	
	//If there isn't a timestamp, or we are forced, or the timestamp is old, we have to update
	$options = get_option('widget_irate');
	if($options['timestamp']=='' || $force || $options['timestamp']+IRATE_CACHE_AGE<time()) {
		$update=true;
	}
		
	//If we have to update, go ahead
	if($update) {
		//Set the new timestamp
		$options['timestamp']=time();
		
		//Call iratemyday and ask for the avatar the user has now
		if($data = irate_readfile('http://api.iratemyday.com/User.aspx?u='.$options['username'])) {
			$user = new SimpleXMLElement($data);
			$options['imageurl']=(string) $user->user->imageUrl;
		}
		
		//Call iratemyday and ask for the last rate the user sends
		if($data = irate_readfile('http://api.iratemyday.com/RatingList.aspx?u='.$options['username'])) {
			$rate = new SimpleXMLElement($data);
			$options['comment']=(string) $rate->ratingList->rating[0]->comment;
			$options['readurl']=(string) $rate->ratingList->rating[0]->readUrl;
		}
		
		//Update the widget with the new timestamp, avatar, rate and url to comment the rate
		update_option('widget_irate', $options);
	}
}

/**
* Returns the HTML code to show the rate in the widget.
*
* @access public
* @return string The HTML code.
*/
function irate_content( ) {
	// Update the rate, maybe the timestamp is old
	irate_update( );

	// Get the options and write the rate
	$options = get_option('widget_irate');
	$answer = "<table><tr><td><div class='irate-box'><div class='irate-img'><a href='http://www.iratemyday.com'><img src='".$options['imageurl']."' border='0' class='irate' alt='iRate'/></a></div>".$options['comment']." <div class='irate-comment'>[<a href='".$options['readurl']."'>".__('Comments', 'irate')."</a>]</div></div></td></tr></table>";
	return $answer;
}

// irate widget stuff
function widget_irate_init() {

	if( !function_exists('register_sidebar_widget') )
		return;

	function widget_irate($args) {
	
		//Suppose we don't have to update
		$update=false;
		
		//If there isn't a timestamp, or we are forced, or the timestamp is old, we have to update
		$options = get_option('widget_irate');
		if($options['timestamp']=='' || $options['timestamp']+IRATE_CACHE_AGE<time()) {
			$update=true;
		}

		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
	
		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_irate');
		$title = $options['title'];
	
		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;

		if($update) { //Use ajax as we have an old cache
			$url=get_bloginfo('wpurl').'/wp-content/plugins/irate/ajax/content.php';
			$nonce = wp_create_nonce('irate');
			echo "<div id='irate_content'><table><tr><td><img src='".get_bloginfo('wpurl')."/wp-content/plugins/irate/img/loading.gif' alt='RSS' border='0' /></td><td>".__('Loading iRate...','irate')."</td></tr></table></div><script type='text/javascript'>irate_feed();</script>";
		} else { //We have a new cache, use it
			echo "<div id='irate_content'>". irate_content() ."</div>";
		}
		
		echo $after_widget;
	}
	
	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_irate_control() {
	
		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_irate');
		
		
		// If there isn't an options array, create one with the default values
		if( !is_array($options) )
			$options = array('title'=>'', 'username'=>'', 'timestamp'=>'', 'imageurl'=>'', 'comment'=>'', 'readurl'=>'');
		if( isset($_POST['irate-submit']) && $_POST['irate-submit']) {
			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['irate_title']));
			// Save the old user name to check if we have to force an upate in the rate
			$old_username = $options['username'];
			$options['username'] = strip_tags(stripslashes($_POST['irate_username']));	
			$options['timestamp']==time()-IRATE_CACHE_AGE-1;
			// Update the option array with the data from the form
			update_option('widget_irate', $options);
			// Suppose we don't need to force to update the rate
			$force=false;
			// If the new username is different from the old username, force the update in the rate
			if($old_username != $options['username']) $force = true;
			irate_update($force);
		}

		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$username = htmlspecialchars($options['username'], ENT_QUOTES);
		$timestamp = $options['timestamp'];

	 
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		require('templates/irate_widget.php');
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	wp_register_sidebar_widget( 'iRate', 'iRate', 'widget_irate', array('iRate', 'widgets'));
	
	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	wp_register_widget_control( 'iRate', 'iRate', 'widget_irate_control', array('iRate', 'widgets'));
}

/**
* A kind of readfile function to determine if use Curl or fopen.
*
* @access public
* @param string filename URI of the File to open
* @return The content of the file
*/
function irate_readfile($filename)
{
	//Just to declare the variables
	$data = false;
	$have_curl = false;
	$local_file = false;
	
	if(function_exists(curl_init)) { //do we have curl installed?
		$have_curl = true;
	}
	
	$search = "@([\w]*)://@i"; //is the file to read a local file?
	if (!preg_match_all($search, $filename, $matches)) {
		$local_file = true;
	}
	
	if($local_file) { //A local file can be handle by fopen
		if($fop = @fopen($filename, 'r')) {
			$data = null;
			while(!feof($fop))
				$data .= fread($fop, 1024);
			fclose($fop);
		}
	} else { //Oops, an external file
		if($have_curl) { //Try with curl
			if($ch = curl_init($filename)) {
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$data=curl_exec($ch);
				curl_close($ch);
			}
		} else { //Try with fsockopen
			$url = parse_url($filename);
			if($fp = fsockopen($url['host'], 80)) {
				//Send GET request
				fputs($fp, "GET " . $url['path'] . "?" . $url['query'] . " HTTP/1.1\r\n");
				fputs($fp, "HOST: " . $url['host'] . " \r\n");
				fputs($fp, "Connection: close \r\n\r\n");
				 
				//Read data
				while(!feof($fp))
				    $data .= fgets($fp, 1024);
				fclose($fp);
				
				$chunked = false;
				$http_status = trim(substr($data, 0, strpos($data, "\n")));
				if ( $http_status != 'HTTP/1.1 200 OK' ) {
					die('The web service endpoint returned a "' . $http_status . '" response');
				}
				if ( strpos($data, 'Transfer-Encoding: chunked') !== false ) {
					$temp = trim(strstr($data, "\r\n\r\n"));
					$data = '';
					$length = trim(substr($temp, 0, strpos($temp, "\r")));
					while ( trim($temp) != "0" && ($length = trim(substr($temp, 0, strpos($temp, "\r")))) != "0" ) {
						$data .= trim(substr($temp, strlen($length)+2, hexdec($length)));
						$temp = trim(substr($temp, strlen($length) + 2 + hexdec($length)));
					}
				} elseif ( strpos($data, 'HTTP/1.1 200 OK') !== false ) {
					$data = trim(strstr($data, "\r\n\r\n"));
				}
			}
		}
	}

	return $data;
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_irate_init');

?>
