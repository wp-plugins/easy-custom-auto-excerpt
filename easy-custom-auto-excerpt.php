<?php
/*
Plugin Name: Easy Custom Auto Excerpt
Plugin URI: http://www.tonjoo.com/easy-custom-auto-excerpt/
Description: Auto Excerpt for your post on home, search and archive.
Version: 0.92
Author: Todi Adiyatmo Wijoyo
Author URI:  http://todiadiyatmo.com
*/
add_filter( 'the_content', 'tonjoo_ecae_execute' );

function tonjoo_ecae_execute($content,$width=400){

	//ambil options dari db
	$options = get_option('tonjoo_ecae_options');



	$width=$options['width'];
	$justify=$options['justify'];
	
	if($options['home']=="yes" && is_home()){
		return tonjoo_ecae_excerpt($content, $width,$justify);
	}
		
	if($options['archive']=="yes" && is_archive()){
		return tonjoo_ecae_excerpt($content, $width,$justify);
	}
	
	if($options['search']=="yes" && is_search()){
		return tonjoo_ecae_excerpt($content, $width,$justify);
	}
	
	else {
		return $content;
	}
	
}


function tonjoo_ecae_excerpt($content, $width, $justify) {

	$options = get_option('tonjoo_ecae_options');

	add_action('wp_enqueue_scripts', 'tonjoo_ecae_scripts', 10000);
		$content = substr(strip_tags($content),0,$width);
		$link = get_permalink();
		$content .= "... <a href='$link'>{$options['read_more']}</a>";
		if($justify!="no") {
			$content = "<div style='text-align:$justify'>" . $content . "</div>";
		}
		return $content;
	}

require_once( plugin_dir_path( __FILE__ ) . 'tonjoo-library.php');
//load option page
require_once( plugin_dir_path( __FILE__ ) . 'options-page.php');