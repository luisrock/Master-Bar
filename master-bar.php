<?php
/**
 * Plugin Name: Master Bar
 * Plugin URI: http://www.luisrock.com
 * Description: Display a bar on top of your wordpress site.
 * Version: 1.0
 * Author: Luis Rock
 * Author URI: http://www.luisrock.com
 * License: GPL2
 */


if ( ! defined( 'ABSPATH' ) ) exit;

//including redux admin

include_once( plugin_dir_path( __FILE__ ) . 'admin/admin-init.php');

//loading CSS file
//no need for now - maybe in future updates

//add_action( 'wp_enqueue_scripts', 'masterbar_load_styles');
//
//function masterbar_load_styles() {
//
//	wp_register_style( 'mab-styles', plugins_url( 'css/mab-styles.css', __FILE__));
//	wp_enqueue_style( 'mab-styles' );
//
//}


//loading JQuery to allow hide bar
add_action('wp_enqueue_scripts', 'master_bar_disappear');
	
	function master_bar_disappear() {
	wp_enqueue_script( 'masterbar_hide', plugins_url( 'js/masterbar-hide.js', __FILE__ ), array( 'jquery' ), '', true );
}

// Localization
add_action('init', 'mab_action_init');
	
	function mab_action_init() {
	load_plugin_textdomain('masterbar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}


//Placing the bar at the top
add_action( 'wp_head', 'masterbar_main_function' );

//Defining plugin's main function

	function masterbar_build_function() {

//Defining variables and defaults to be used in the function

	global $masterbar_settings;

	//shadow 
	 	$shadow = $masterbar_settings['mab-shadow'];
	 
	//text and alternative text
		$text = $masterbar_settings['mab-text'];
		$checkalttext = $masterbar_settings['mab-checkalttext'];
	 	$alttext = $masterbar_settings['mab-alttext'];
	
	//text link and alternative text link
	 	$linktext = $masterbar_settings['mab-linktext'];
	 	$linkalttext = $masterbar_settings['mab-linkalttext'];
	
	//URL text link and URL alternative text link
	 	$urllinktext = $masterbar_settings['mab-urllinktext'];
	 	$urllinkalttext = $masterbar_settings['mab-urllinkalttext'];
	
	//link new window
	 	$linkwindow = $masterbar_settings['mab-linkwindow'];
	
	//hide 
	 	$hide = $masterbar_settings['mab-hide'];
	
	//css
	 	$css = $masterbar_settings['mab-css'];
	

//The bar itself	

     	$bar =  '<div class="masterbar"';

	 	$bar .= 'style="';
	
	   		if ($shadow == 1) 	{
	 	$bar .= 'text-shadow: 1px 1px 1px #000;';
				}
		
		
	 	$bar .= $css . '">';

		
	
// If (ternary syntax) the user is logged and isset $alttext, show alternative text. Else, show $text = default text. 				
	 
		$bar .=  ( (is_user_logged_in() && $checkalttext && !empty($alttext) ) ? $alttext : $text ) . '  ';
	
// links and URL

		$bar .= '<a class="bar-link" href="' . ( (is_user_logged_in() && $checkalttext  ) ? $urllinkalttext : $urllinktext ) . '"';
	
		$bar .= (  $linkwindow ? ' target="_blank"' : '') . '>';

		$bar .= ( (is_user_logged_in() && $checkalttext  ) ? $linkalttext : $linktext ) . '</a>';

//If answer to "Let Users Hide Bar?" is "yes", show the "x" image and use the span class that triggers the jquery function.	
		
			if ($hide == 1) 	{
	 	$bar .= '<span class="mbhide" style="float:right"><a href=""><img src="' . plugins_url( 'images/x-white.png' , __FILE__ ) . '"></a></span>';
			}
	
	  	$bar .= '</div>';
	
			return $bar;
	}
	
	function masterbar_main_function() {
		
//		
		global $masterbar_settings;
		$all = $masterbar_settings['mab-all'];
		$main = $masterbar_settings['mab-main'];
		$home = $masterbar_settings['mab-home'];
		$local = $masterbar_settings['mab-post'];
		$current_post = get_post_type ();

//		
//
//		
//	
//checking if the answer to "Activate Bar?" is "yes"
//
			if ( $main == 1 )	{

//showing BAR all over the website
//
			if ( $all == 1 ) {

			echo masterbar_build_function();
		}

//showing BAR only at the home page if is selected
		
			if ( $all != 1 && $home == 1 && is_front_page() ) {
			
			echo masterbar_build_function();
			}
		
//showing BAR only at selected post types

			if ( $all != 1 && $home != 1 && is_array($local) && in_array($current_post, $local) ) {
		
			echo masterbar_build_function();
			}
		}
	
	}

//Shortcode (help get from MAXIME at http://stackoverflow.com/questions/9558211/use-wordpress-shortcode-to-add-meta-tags)

// Function to hook to "the_posts" (just edit the two variables)

	function masterbar_metashortcode_mycode( $posts ) {
	

  		$shortcode = 'masterbar';
  		$callback_function = 'masterbar_shortcode';

  		return masterbar_metashortcode_shortcode_to_wphead( $posts, $shortcode, $callback_function );
}

// To execute when shortcode is found
	function masterbar_shortcode() {
					
			global $masterbar_settings;
			$all = $masterbar_settings['mab-all'];
			$main = $masterbar_settings['mab-main'];

							
				 	if ( $main == 1 && $all != 1 ) {

					echo masterbar_build_function();
				}
}

// look for shortcode in the content and apply expected behaviour (don't edit!)
	function masterbar_metashortcode_shortcode_to_wphead( $posts, $shortcode, $callback_function ) {
  				
					if ( empty( $posts ) )
				    return $posts;

			 $found = false;
  				foreach ($posts as $post) {
    				if ( stripos( $post->post_content, '[' . $shortcode ) !== false ) {
      	add_shortcode( $shortcode, '__return_null');
      		$found = true;
	   break;
   	 }
  }

  				if ( $found )
	    add_action('wp_head', $callback_function );

  		return $posts;
}

// Instead of creating a shortcode, hook to the_posts
add_action('the_posts', 'masterbar_metashortcode_mycode');







