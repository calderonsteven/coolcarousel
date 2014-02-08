<?php 
/* 
Plugin Name: coolcarousel
Plugin URI: http://calderonsteven.github.io/
Version: 0
Author: Steven Calderon http://calderonsteven.github.io/
Description: a simple shortcode for a minimalist responsive image carousel
*/  

//Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
add_action( 'wp_enqueue_scripts', 'prefix_stylesheet_coolslider' );

//nqueue plugin style-file/
function prefix_stylesheet_coolslider() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}


//[carousel]
function pn_get_attachment_id_from_url( $attachment_url = '' ) {
 
	global $wpdb;
	$attachment_id = false;
 
	// If there is no url, return.
	if ( '' == $attachment_url )
		return;
 
	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();
 
	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
 
		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
 
		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
 
		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
 
	}
 
	return $attachment_id;
}

function carousel_func( $atts,  $content) {
	do_shortcode ($content);

	extract( shortcode_atts( array(
		'height' => '370',
	), $atts ) );

	$scriptText = '<script>
	jQuery(window).load(function() {
	  	var s=0,
	  		interval=0;

		jQuery.each(jQuery("#cool-carousel img"), function(i,o){ s+= jQuery(o).width() });
		jQuery("#cool-carousel ul").width(s);	

		jQuery("#cool-carousel").hover(function(){
			//stop
			clearInterval(interval);
		},function(){
			//start
			interval = setInterval(function(){
				jQuery("#cool-carousel").scrollLeft( jQuery("#cool-carousel").scrollLeft() + 1 );
			}, 10);
		});

	});</script>';

	return '<div id="cool-carousel"> <ul>'.do_shortcode ($content).'</ul></div>'.$scriptText;
}
add_shortcode( 'carousel', 'carousel_func' );

function carouselImage_func( $atts, $content) {
	do_shortcode ($content);
	
	extract( shortcode_atts( array(
		'url' => 'no-url',
	), $atts ) );

	$attachment_id = pn_get_attachment_id_from_url($atts["url"]);
	$urlMedium = wp_get_attachment_image_src( $attachment_id, "large");


	return '<li> <img src="'.$urlMedium[0].'" > </li>';
	//return '<img src="'.$atts["url"].'" >';
}
add_shortcode( 'carouselImage', 'carouselImage_func' );

?>
